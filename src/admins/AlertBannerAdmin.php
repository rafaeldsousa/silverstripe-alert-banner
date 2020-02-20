<?php

namespace DNADesign\AlertBanner;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridField;

class AlertBannerAdmin extends ModelAdmin
{
  private static $managed_models = [
    AlertBanner::class
  ];

  private static $url_segment = 'alerts';

  private static $menu_title = 'Alerts';


  // private static $menu_icon = 'mysite/images/icons/alert.png';

  private static $menu_priority = 0.3;

  public function getEditForm($id = null, $fields = null)
  {
    $form = parent::getEditForm($id, $fields);

    // Remove the Delete button from the grid items because the admin MUST
    // edit each item, then Unpublish and then Delete in order to
    // completely remove the alert.

    $gridField = $form->Fields()->dataFieldByName($this->sanitiseClassName($this->modelClass));
    if ($gridField && $gridField instanceof GridField) {
      $config = $gridField->getConfig();
      $config->removeComponentsByType('GridFieldDeleteAction');
    }

    return $form;
  }
}
