<?php

namespace DNADesign\AlertBanner;

use gorriecoe\Link\Models\Link;
use gorriecoe\LinkField\LinkField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Versioned\Versioned;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;


class Alert extends DataObject implements PermissionProvider
{
    private static $db = array(
        'Title' => 'Text',
        'Description' => 'HTMLText',
        'Global' => 'Boolean',
        'Scheme' => 'Varchar(15)',
    );

    private static $has_one = [
        'DisplayedPage' => SiteTreeLink::class,
        'ButtonLink' => Link::class
    ];

    private static $many_many = [
        'Exceptions' => SiteTreeLink::class
    ];

    private static $many_many_extraFields = [
        'Exceptions' => [
            'Sort' => 'Int' // Required for all many_many relationships
        ]
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'FormattedDisplay' => 'Alert shown',
        'FormattedGlobal' => 'Show on all pages',
        'FormattedShowSinglePage' => 'Show on single page'
    ];

    private $schemes = array(
        'red' => 'Cross',
        'green' => 'Tick',
        'fact' => 'Fact',
        'tip' => 'Tip',
        'exclamation-mark' => 'Exclamation mark',
        'question' => 'Question mark'
    );

    private static $default_sort = 'ID DESC';

    public function FormattedGlobal()
    {
        return $this->Global ? true : false;
    }

    public function FormattedEmergency()
    {
        return $this->Emergency ? true : false;
    }

    public function FormattedDisplay()
    {
        return $this->getDisplayed() ? true : false;
    }

    public function FormattedShowSinglePage()
    {
        if ($this->Global) {
            return true;
        } else {
            return $this->ShowSinglePage ? true : false;
        }
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Exceptions');
        $fields->removeByName('Description');
        $fields->removeByName('ButtonLinkID');
        $fields->removeByName('DisplayedPageID');
        $fields->removeByName('AlertIconID');
        $fields->removeByName('EmergencyIconID');
        $fields->removeByName('TitleLinkID');

        $fields->addFieldsToTab('Root.Main', array(
            $title = TextField::create('Title'),
            $description = HTMLEditorField::create('Description'),

            $global = CheckboxField::create('Global', 'Show on all pages'),

            $buttonlink = Wrapper::create(
                LinkField::create('ButtonLink', 'Button link', $this)
            ),

            $displayedPage = Wrapper::create(
                LinkField::create('DisplayedPage', 'Alert Page', $this)
            ),

            $exceptions = Wrapper::create(LinkField::create(
                'Exceptions',
                'Exceptions',
                $this
            )->setSortColumn('Sort'))
        ));

        $fields->addFieldsToTab('Root.Style', array(
            DropdownField::create('Scheme', 'Scheme', $this->schemes)
        ));

        $displayedPage->hideIf('Global')->isChecked()->end();
        $exceptions->hideUnless('Global')->isChecked()->end();

        return $fields;
    }

    public function getDisplayed()
    {
        if ($isPublished = Versioned::get_by_stage(Alert::class, 'Live')->byID($this->ID)) {
            return 1;
        }
        return 0;
    }

    public function providePermissions()
    {
        return array(
            'ALERT_EDIT' => array(
                'name' => 'Create/edit/publish Alerts',
                'category' => 'Alerts',
            )
        );
    }

    public function canView($member = null)
    {
        return true;
    }

    public function canEdit($member = null)
    {
        return Permission::check('ALERT_EDIT');
    }

    public function canDelete($member = null)
    {
        return Permission::check('ALERT_EDIT');
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('ALERT_EDIT');
    }

    public function canPublish($member = null)
    {
        return Permission::check('ALERT_EDIT');
    }
}
