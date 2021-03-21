<?php

namespace DNADesign\AlertBanner;

use SilverStripe\Assets\Image;
use gorriecoe\Link\Models\Link;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use SilverStripe\Forms\TextField;
use gorriecoe\LinkField\LinkField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Forms\TreeDropdownField;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use SilverStripe\Security\PermissionProvider;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class AlertBanner extends DataObject implements PermissionProvider
{
    private static $db = [
        'Title' => 'Text',
        'Description' => 'HTMLText',
        'Global' => 'Boolean',
        'DisableDismiss' => 'Boolean',
        'CurrentTheme' => 'Varchar(256)'
    ];

    private static $table_name = "SiteAlertBanner";

    private static $controller = AlertBannerController::class;

    private static $has_one = [
        'DisplayedPage' => SiteTreeLink::class,
        'ButtonLink' => Link::class
    ];

    private static $many_many = [
        'Exceptions' => SiteTreeLink::class,
        'MustShowPages' => SiteTreeLink::class,
    ];

    private static $many_many_extraFields = [
        'Exceptions' => [
            'Sort' => 'Int', // Required for all many_many relationships
        ],
        'MustShowPages' => [
            'Sort' => 'Int', // Required for all many_many relationships
        ]
    ];

    private static $summary_fields = [
        'ID' => 'ID',
        'Title' => 'Title',
        'FormattedDisplay' => 'Alert displayed',
        'Global.Nice' => 'Show on all pages',
        'FormattedShowSinglePage' => 'Show on single page',
        'getThemeTitle' => 'CurrentTheme'
    ];

    private static $searchable_fields = [
        'Title',
    ];

    private static $extensions = [
        Versioned::class,
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
        $fields->removeByName('MustShowPages');
        $fields->removeByName('Description');
        $fields->removeByName('ButtonLinkID');
        $fields->removeByName('DisplayedPageID');
        $fields->removeByName('DisableDismiss');
        $fields->removeByName('TitleLinkID');
        $fields->removeByName('CurrentTheme');

        // SS 4.5 workaround
        if ($this->isInDB()) {
            $fields->addFieldsToTab(
                'Root.Main', [
                    $title = TextField::create('Title')
                        ->setDescription('Reference only.'),
                    $description = HTMLEditorField::create('Description', 'Content')
                        ->setDescription('Use this field to define your alert banner content.'),

                    $global = CheckboxField::create('Global', 'Show on all pages'),

                    $buttonlink = Wrapper::create(
                        LinkField::create('ButtonLink', 'Button link', $this)
                    ),

                    $displayedPage = Wrapper::create(
                        TreeDropdownField::create('DisplayedPageID', 'Alert Page', SiteTree::class)
                    ),

                    $exceptions = Wrapper::create(
                        LinkField::create(
                            'Exceptions',
                            'Exceptions',
                            $this
                        )->setSortColumn('Sort')
                            ->setDescription('This will not show the alert on this page and any child pages below it.')
                    ),

                    $mustShowPages = Wrapper::create(
                        LinkField::create(
                            'MustShowPages',
                            'MustShowPages',
                            $this
                        )->setSortColumn('Sort')
                            ->setDescription('If this page falls as a child of an exception page it will display the alert.')
                    )
                ]
            );

            $displayedPage->hideIf('Global')
                ->isChecked()
                ->end();

            $exceptions->hideUnless('Global')
                ->isChecked()
                ->end();

            $mustShowPages->hideUnless('Global')
                ->isChecked()
                ->end();
        } else {
            $fields->addFieldsToTab(
                'Root.Main', [
                    $title = TextField::create('Title')
                        ->setDescription('Reference only.'),
                    $description = HTMLEditorField::create('Description', 'Content')
                        ->setDescription('Use this field to define your alert banner content.'),

                    $global = CheckboxField::create('Global', 'Show on all pages'),
                ]
            );
        }

        $fields->addFieldToTab(
            'Root.Main',
            CheckboxField::create(
                'DisableDismiss',
                'Hide dismiss button'
            )->setDescription('Hiding the dismiss button removes the users ability to dismiss the alert banner'),
            'Description'
        );

        $fields->insertAfter(
            'DisableDismiss',
            DropdownField::create(
                'CurrentTheme',
                'Theme',
                $this->getThemes()
            )
        );

        return $fields;
    }

    public function getDisplayed()
    {
        if ($this->Global || $this->DisplayedPageID) {
            if ($isPublished = Versioned::get_by_stage(AlertBanner::class, 'Live')->byID($this->ID)) {
                return 1;
            }
        }

        return 0;
    }

    public function getThemes()
    {
        return Config::inst()->get('DNADesign\AlertBanner\AlertBanner', 'Themes');
    }

    public function providePermissions()
    {
        return array(
            'ALERT_EDIT' => array(
                'name' => 'Create/edit/publish Alerts',
                'category' => 'Alerts',
            ),
        );
    }

    public function getThemeTitle()
    {
        if (!$this->dbObject('CurrentTheme')->exists()) {
            return false;
        }

        return Config::inst()->get('DNADesign\AlertBanner\AlertBanner', 'Themes')[$this->CurrentTheme];
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
