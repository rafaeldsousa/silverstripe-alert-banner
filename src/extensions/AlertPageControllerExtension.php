<?php

/**
 * This extension adds the ability to control the max-age per originator.
 * The configuration option is surfaced to the CMS UI. The extension needs to be added
 * to the object related to the policed controller.
 */
class AlertPageControllerExtension extends DataExtension
{

  private static $allowed_actions = array(
    'setBannerApplies'
  );

  public function onAfterInit()
  {
    if ($this->isModuleLiveReload()) {
      $port = Config::inst()->get($this->class, 'live_reload_port');
      Requirements::javascript(sprintf('http://localhost:%s/livereload.js', $port));
    } else {
      Requirements::css(ALERT_PATH . '/client/dist/main.css');
    }

    Requirements::javascript(ALERT_PATH . '/client/dist/main.js');
  }

  public function isModuleLiveReload()
  {
    $isDev = Director::isDev();
    $port = Config::inst()->get($this->class, 'live_reload_port');
    $fSockOpen = @fsockopen('localhost', $port, $errno, $errstr, 1);

    return $isDev && $fSockOpen;
  }

  public function setBannerApplies($data)
  {
    Session::add_to_array('Alerts', $data->postVar('id'));
  }

  public function getAlertBanners()
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
    $alerts = Session::get('Alerts');
    $show = true;

    if ($alert->Global == 1) {
      $exceptions = array_map(function ($exception) {
        if ($exception->Type === "SiteTree") {
          return $exception->SiteTree()->ID;
        }
        return null;
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
