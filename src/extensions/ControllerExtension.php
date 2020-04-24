<?php

namespace DNADesign\AlertBanner;

use SilverStripe\View\Requirements;
use SilverStripe\Core\Extension;

/**
 * This extension adds the ability to control the max-age per originator.
 * The configuration option is surfaced to the CMS UI. The extension needs to be added
 * to the object related to the policed controller.
 */
class ControllerExtension extends Extension
{
    public function onAfterInit()
    {
        Requirements::css('dnadesign/silverstripe-alert-banner: client/dist/main.css');
        Requirements::javascript('dnadesign/silverstripe-alert-banner: client/dist/main.js');
    }

    public function getAlertBanners()
    {
        $alerts = AlertBanner::get()->filterByCallback(function ($alert) {
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
        $session = $this->owner->getRequest()->getSession();
        $alerts = $session->get('AlertBanners');
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

            if ($this->owner->ID !== $alert->DisplayedPageID) {
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
