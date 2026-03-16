<?php
namespace ElliotSawyer\TypesenseSearch;

use ElliotSawyer\SilverstripeTypesense\Collection;
use Page;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\View\ArrayData;
use Symbiote\MultiValueField\Fields\KeyValueField;

class TypesenseSearchPage extends Page
{
    private static $table_name = 'TypesenseSearchPage';
    private static $singular_name = 'Search page';
    private static $plural_name = 'Search pages';

    private static $db = [
        'SelectedQueryFields'  => 'Text',
        'SelectedFacetFields'  => 'Text',
        'SelectedSortFields'  => 'Text',
        'PreselectedFacets' => 'MultiValueField',
        'SearchAPIKey' => 'Varchar(255)'
    ];

    private static $has_one = [
        'Collection' => Collection::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName(['CollectionID', 'SelectedQueryFields', 'SelectedFacetFields', 'SelectedSortFields']);
        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create(
                'CollectionID',
                'Collection',
                Collection::get()
                    ->map('ID', 'Name')
                    ->toArray()
            )
        , 'Metadata');
        if($this->CollectionID) {
            $hasAutoField = $this->Collection()->Fields()->find('type', 'auto');
            $facetFields = $this->Collection()->Fields()
                ->filter(['index' => 1, 'facet' => true])
                ->map('name', 'name');

            $fields->addFieldsToTab(
                'Root.Main',
                [
                    $hasAutoField
                        ? TextField::create('SelectedQueryFields', 'Query fields')
                            ->setDescription('An auto field was detected, you must specify these fields manually as a comma-separated list (i.e. Field1,Field2,FieldX)')
                        : ListboxField::create('SelectedQueryFields', 'Query fields',
                            $this->Collection()->Fields()
                                ->filter(['index' => 1, 'type' => ['string', 'string[]']])
                                ->map('name', 'name')
                    ),
                    ListboxField::create(
                        'SelectedFacetFields',
                        'Facet fields',
                        $facetFields
                    ),
                    ListboxField::create(
                        'SelectedSortFields',
                        'Sort fields',
                        $this->Collection()->Fields()
                            ->filter(['index' => 1])
                            ->filterAny([
                                'type' => ['int32', 'int64', 'float', 'int32[]', 'int64[]', 'float[]'],
                                'sort' => 1,
                            ])
                            ->map('name', 'name')
                    ),
                    KeyValueField::create('PreselectedFacets', 'Pre-selected facet fields', $facetFields)
                        ->setDescription(
                            'Format: <strong>Title:Some string,Author:Some name</strong>.
                            This is useful when the search needs to be limited to a subset of the entire collection.
                        ')
                ]
            );
        }

        $fields->addFieldToTab('Root.Main', TextField::create('SearchAPIKey'));
        return $fields;
    }

    public function CollectionName()
    {
        return $this->Collection()->Name;
    }

    public function getSelectedFields($type = null) {
        $selectedField = '';
        switch($type) {
            case 'Query':
            case 'Sort':
            case 'Facet':
                $selectedField = 'Selected'.$type.'Fields';
                break;
        }
        $data = $this->$selectedField;
        if($data && $data = json_decode($data, true)) {
            $list = ArrayList::create();
            foreach($data as $d) {
                $list->push(ArrayData::create([
                    'Field' => $d
                ]));
            }
            return $list;
        }

        return null;
    }

    public function QuerySearchParameters()
    {
        $qsp = $this->SelectedQueryFields;
        if($qsp) {
            if($qsp = json_decode($qsp, true)) {
                return implode(',', $qsp);
            } else {
                $qsp = $this->SelectedQueryFields;
                // $qsp = explode(',', $qsp);
                //test that it's a CSV list first
                $qsp = str_getcsv($qsp);
                $qsp = array_filter($qsp);
                $qsp = implode(',', $qsp);

                //if there are any non-alphanumeric characters or commas in the list, return an empty string
                if(preg_match('/[^A-Za-z0-9,]+/', $qsp, $matches)) {
                    return '';
                }
                return $qsp;
            }
        }
        return '';
    }

    public function JSPreselectedFacets()
    {
        $values = $this->PreselectedFacets->getValue();
        if(!$values) {
            return '';
        }

        $out = [];
        foreach($values as $k => $v) {
            $out[] = "'$k:$v'";
        }

        $out2 = implode(',', $out);

        return $out2;
    }

    public function DataAttributes()
    {
        $out = '';
        $siteConfig = SiteConfig::current_site_config();

        $attrs = [
           'protocol' => $siteConfig->TypesenseProtocol,
           'hostname' => $siteConfig->TypesenseHostname,
           'port' => $siteConfig->TypesensePort,
           'path' => $siteConfig->TypesensePath,
           'apikey' => $this->SearchAPIKey,
           'collection' => $this->Collection()->Name,
           'queryfields' => $this->getSelectedFields('Query'),
           'facetfields' => $this->SelectedFacetFields ? implode(',', json_decode($this->SelectedFacetFields ?? '')) : null,
           'sortfields' => '',
           'preselectedfacetfields' => $this->PreselectedFacetFields,
        ];

        foreach($attrs as $key => $val) {
            if($val) {
                $out .= 'data-'.$key.'="'.$val.'" ';;
            }

        }

        return $out;
    }

    public function IsSearchPage()
    {
        return $this instanceof TypesenseSearchPage;
    }
}
