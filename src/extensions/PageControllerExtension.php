<?php

namespace DNADesign\AlertBanner;

use SilverStripe\Control\Director;
use SilverStripe\ORM\DataExtension;
use SilverStripe\View\Requirements;

/**
 * This extension adds the ability to control the max-age per originator.
 * The configuration option is surfaced to the CMS UI. The extension needs to be added
 * to the object related to the policed controller.
 */
class PageControllerExtension extends DataExtension
{

  private static $allowed_actions = array(
    'setBannerApplies'
  );

  public function onAfterInit()
  {
    if ($this->isModuleLiveReload()) {
      Requirements::javascript(sprintf('http://localhost:%s/livereload.js', $this->owner->config()->live_reload_port));
    } else {
      Requirements::css('dnadesign/silverstripe-alert-banner: client/dist/main.css');
    }

    Requirements::javascript('dnadesign/silverstripe-alert-banner: client/dist/main.js');
  }

  public function isModuleLiveReload()
  {
    $isDev = Director::isDev();
    $fSockOpen = @fsockopen('localhost', $this->owner->config()->live_reload_port, $errno, $errstr, 1);

    return $isDev && $fSockOpen;
  }

  public function getSession()
  {
    return $this->owner->getRequest()->getSession();
  }

  public function setBannerApplies($data)
  {
    $session = $this->getSession();

    $alerts = $session->get('Alerts');

    $sessionData = $alerts ?: [];
    array_push($sessionData, $data->postVar('id'));
    $session->set('Alerts', $sessionData);
  }

  public function getAlerts()
  {
    $alerts = Alert::get()->filterByCallback(function ($alert) {
      return $this->alertCanShow($alert);
    })->sort(array(
      // Prioritise Global and Emergency Alerts
      'Global' => 'DESC',
      'Emergency' => 'DESC'
    ));
    return $alerts;
  }

  public function alertCanShow($alert)
  {
    $session = $this->getSession();
    $alerts = $session->get('Alerts');
    $show = true;

    if ($alert->Global == 1) {
      $exceptions = array_map(function ($exception) {
        return $exception->getLinkedPageID();
      }, $alert->Exceptions()->toArray());

      if (!empty($exceptions)) {
        if (in_array($this->owner->ID, $exceptions)) {
          return false;
        }
      }
    } else {
      $displayedPage = $alert->DisplayedPage;
      if (empty($displayedPage)) {
        return false;
      }

      if ($this->owner->ID !== $displayedPage->getLinkedPageID()) {
        return false;
      }
    }

    if (empty($alerts)) {
      $show = true;
    } else {
      $show = !in_array($alert->ID, $alerts);
    }

    return $show;
  }
}
