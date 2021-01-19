<?php

namespace DNADesign\AlertBanner;

use SilverStripe\Assets\File;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;
use Silverstripe\Control\NullHTTPRequest;

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

    public function getAllowedPageIDs($alert)
    {
        $result = [];

        foreach ($alert->MustShowPages() as $page) {
            array_push($result, $page->getLinkedPageID());
        }

        return $result;
    }

    public function getExcludedPageIDs($alert)
    {
        $result = [];

        foreach ($alert->Exceptions() as $page) {
            array_push($result, $page->getLinkedPageID());
        }

        return $result;
    }

    public function getParentIDs() {
        $result = [$this->owner->ID];

        foreach ($this->owner->getAncestors() as $page) {
            array_push($result, $page->ID);
        }

        return $result;
    }

    public function alertCanShow($alert)
    {
        $request = $this->owner->getRequest();

        if ($request instanceof NullHTTPRequest) {
            return false;
        }

        $session = $request->getSession();
        $alerts = $session->get('AlertBanners');
        $show = true;

        if ($alert->Global == 1) {
            if (!$alert->Exceptions()) {
                return true;
            }

            $exceptionIDs = $this->getExcludedPageIDs($alert);
            $allowedPages = $this->getAllowedPageIDs($alert);
            $parentIDs = $this->getParentIDs();

            /**
             * Check to see if this page is in the must show pages list
             */
            if (array_intersect($allowedPages, [$this->owner->ID])) {
                return true;
            }

            /**
             * Checks to see if this page, or its parents are in the exception list
             */
            if (array_intersect($exceptionIDs, $parentIDs)) {
                return false;
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

    /**
     * Get the name of a file sans extension
     * @param String/Int $fileId
     * @return String || bool
     */
    public function getFileName($fileId)
    {
        $file = File::get()->byID($fileId);
        if (!$file) {
            return false;
        }
        return explode('.' . $file->getExtension(), $file->Name)[0];
    }

    /**
     * Get the directory of a file sans filename
     * @param $fileID | - The ID of a file
     * @return
     */
    /**
     * @param String/Int $fileId  - The ID of a file
     * @return String || bool
     */
    public function getFileDir($fileId)
    {
        $file = File::get()->byID($fileId);
        if (!$file) {
            return false;
        }
        $filename = explode($file->Name, $file->getURL())[0];
        $shortPath = explode('assets', $filename)[1];

        return 'public/assets' . $shortPath;
    }
}
