<?php

namespace DNADesign\AlertBanner;

use SilverStripe\Control\Controller;

/**
 * This extension adds the ability to control the max-age per originator.
 * The configuration option is surfaced to the CMS UI. The extension needs to be added
 * to the object related to the policed controller.
 */
class AlertBannerController extends Controller
{
    private static $allowed_actions = array(
        'dismissBanner'
    );

    public function dismissBanner($request)
    {
        $session = $request->getSession();

        $alerts = $session->get('AlertBanners');

        $sessionData = $alerts ?: [];
        array_push($sessionData, $request->getVar('id'));
        $session->set('AlertBanners', $sessionData);
    }
}
