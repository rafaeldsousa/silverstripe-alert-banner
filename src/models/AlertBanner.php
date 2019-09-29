<?php

class AlertBanner extends DataObject implements PermissionProvider
{
    private static $db = array(
        'Title' => 'Text',
        'Description' => 'HTMLText',
        'Global' => 'Boolean',
        'BgColor' => 'Color',
        'FontColor' => 'Color',
    );

    private static $has_one = [
        'DisplayedPage' => 'Link',
        'ButtonLink' => 'Link',
        'Icon' => 'Image'

    ];

    private static $owns = [
        'Icon'
    ];

    private static $many_many = [
        'Exceptions' => 'Link'
    ];

    private static $many_many_extraFields = [
        'Exceptions' => [
            'Sort' => 'Int' // Required for all many_many relationships
        ]
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'FormattedDisplay' => 'Alert Banner shown',
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
        $fields->removeByName('IconID');
        $fields->removeByName('TitleLinkID');

        $ExceptionsGrid = GridFieldConfig_RecordEditor::create();

        $fields->addFieldsToTab('Root.Main', array(
            $title = TextField::create('Title'),
            $description = HTMLEditorField::create('Description'),

            $global = CheckboxField::create('Global', 'Show on all pages'),

            $buttonlink = DisplayLogicWrapper::create(
                LinkField::create('ButtonLink', 'Button link', $this)
            ),

            $displayedPage = DisplayLogicWrapper::create(
                LinkField::create('DisplayedPage', 'Alert Page', $this)
            ),

            $exceptions = DisplayLogicWrapper::create(GridField::create('Exceptions', 'Exceptions', $this->Exceptions(), $ExceptionsGrid))
        ));

        $fields->addFieldsToTab('Root.Style', array(
            ColorField::create('BgColor', 'Background Color')->setDescription('Default Color is blue (#0077af)'),
            ColorField::create('FontColor', 'Font Color')->setDescription('Default Color is white (#FFFFFF)'),
            UploadField::create('Icon', 'Icon')
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
