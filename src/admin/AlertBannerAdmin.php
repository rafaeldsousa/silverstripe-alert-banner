<?php

class AlertBannerAdmin extends ModelAdmin
{
  private static $managed_models = [
    'AlertBanner'
  ];

  private static $url_segment = 'alert-banners';

  private static $menu_title = 'Alert Banners';

  // private static $menu_priority = 0.3;

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
