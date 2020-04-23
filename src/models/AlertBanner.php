<?php

namespace DNADesign\AlertBanner;

use SilverStripe\Assets\Image;
use gorriecoe\Link\Models\Link;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use gorriecoe\LinkField\LinkField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use RyanPotter\SilverStripeColorField\Forms\ColorField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\FieldType\DBField;

class AlertBanner extends DataObject implements PermissionProvider
{
    private static $db = array(
        'Title' => 'Text',
        'Description' => 'HTMLText',
        'Global' => 'Boolean',
        'DisableDismiss' => 'Boolean',
        'BgColor' => 'Varchar(7)',
        'FontColor' => 'Varchar(7)',
    );

    private static $has_one = [
        'DisplayedPage' => SiteTreeLink::class,
        'ButtonLink' => Link::class,
        'AlertIcon' => Image::class

    ];

    private static $owns = [
        'AlertIcon'
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
        'FormattedDisplay' => 'Alert Enabled',
        'Global.Nice' => 'Show on all pages',
        'FormattedShowSinglePage' => 'Show on single page'
    ];

    private static $default_sort = 'ID DESC';

    public function FormattedDisplay()
    {
        return DBField::create_field('Boolean', $this->getDisplayed());
    }

    public function FormattedShowSinglePage()
    {
        if ($this->Global) {
            return DBField::create_field('Boolean', 0);
        } else {
            return DBField::create_field('Boolean', $this->DisplayedPageID ? 1 : 0);
        }
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Exceptions');
        $fields->removeByName('Description');
        $fields->removeByName('ButtonLinkID');
        $fields->removeByName('DisplayedPageID');
        $fields->removeByName('DisableDismiss');
        $fields->removeByName('AlertIconID');
        $fields->removeByName('TitleLinkID');

        // SS 4.5 workaround
        if ($this->isInDB()) {
            $fields->addFieldsToTab('Root.Main', array(
                $title = TextField::create('Title')->setDescription('Reference only.'),
                $description = HTMLEditorField::create('Description', 'Content')->setDescription('Use this field to define your alert banner content.'),

                $global = CheckboxField::create('Global', 'Show on all pages'),

                $buttonlink = Wrapper::create(
                    LinkField::create('ButtonLink', 'Button link', $this)
                ),

                $displayedPage = Wrapper::create(
                    TreeDropdownField::create('DisplayedPageID', 'Alert Page', SiteTree::class)
                ),

                $exceptions = Wrapper::create(LinkField::create(
                    'Exceptions',
                    'Exceptions',
                    $this
                )->setSortColumn('Sort'))
            ));

            $displayedPage->hideIf('Global')->isChecked()->end();
            $exceptions->hideUnless('Global')->isChecked()->end();
        } else {
            $fields->addFieldsToTab('Root.Main', array(
                $title = TextField::create('Title')->setDescription('Reference only.'),
                $description = HTMLEditorField::create('Description', 'Content')->setDescription('Use this field to define your alert banner content.'),

                $global = CheckboxField::create('Global', 'Show on all pages')
            ));
        }

        $fields->addFieldToTab('Root.Main', CheckboxField::create('DisableDismiss', 'Hide dismiss button')->setDescription('Hiding the dismiss button removes the users ability to dismiss the alert banner'), 'Description');

        $fields->addFieldsToTab('Root.Style', array(
            ColorField::create('BgColor', 'Background Color')->setDescription('Default Color is blue (#0077af)'),
            ColorField::create('FontColor', 'Font Color')->setDescription('Default Color is white (#FFFFFF)'),
            UploadField::create('AlertIcon', 'Icon')
        ));

        return $fields;
    }

    public function getDisplayed()
    {
        if ($isPublished = Versioned::get_by_stage(AlertBanner::class, 'Live')->byID($this->ID)) {
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
