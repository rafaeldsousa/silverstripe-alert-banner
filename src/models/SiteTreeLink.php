<?php

namespace DNADesign\AlertBanner;

use gorriecoe\Link\Models\Link;
use SilverStripe\Forms\OptionsetField;

class SiteTreeLink extends Link
{
  public function getCMSFields()
  {
    $fields = parent::getCMSFields();

    $types = [
      'SiteTree' => 'Page on this website'
    ];

    $fields->replaceField('Type', OptionsetField::create(
      'Type',
      _t(__CLASS__ . '.LINKTYPE', 'Type'),
      $types
    )->setValue('URL'));

    return $fields;
  }

  public function getLinkedPageID()
  {
    if (!$this->ID) {
      return;
    }

    $PageID = false;

    if ($component = $this->getComponent('SiteTree')) {
      if (!$component->exists()) {
        $PageID = false;
      }
      $PageID = $component->ID;
    }

    return $PageID;
  }
}
