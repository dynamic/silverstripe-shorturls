<?php

namespace Dynamic\ShortURL\ORM;

use Dynamic\ShortURL\Model\ShortURL;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Core\Extension;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;

class SiteTreeDataExtension extends Extension
{
    /**
     * @var array
     */
    private static $many_many = [
        'ShortURLs' => ShortURL::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->ID) {
            $config = GridFieldConfig_RelationEditor::create()
                ->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
            $urlsField = GridField::create('ShortURLs', 'Campaign URLs', $this->owner->ShortURLs(), $config);
            $fields->addFieldsToTab('Root.Share', $urlsField);
        }
    }
}
