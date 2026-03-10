<?php
namespace ElliotSawyer\TypesenseSearch\ScopedSearch;

use ElliotSawyer\TypesenseSearch\TypesenseSearchPage;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;

class ScopedSearchPage extends TypesenseSearchPage
{
    private static $table_name = 'ScopedSearchPage';
    private static $singular_name = 'Scoped search page';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->datafieldByName('SearchAPIKey')
            ->setTitle('Parent Search Key')
            ->setDescription('For a scoped search, all child keys are derived from this one');
        return $fields;
    }

    public function DataAttributes()
    {
        $out = '';
        $siteConfig = SiteConfig::current_site_config();
        $member = Security::getCurrentUser();

        $attrs = [
           'protocol' => $siteConfig->TypesenseProtocol,
           'hostname' => $siteConfig->TypesenseHostname,
           'port' => $siteConfig->TypesensePort,
           'path' => $siteConfig->TypesensePath,
           'apikey' => $member->TypesenseAPISearchKey,
           'collection' => $this->Collection()->Name,
           'queryfields' => $this->SelectedQueryFields ? implode(',', json_decode($this->SelectedQueryFields ?? '')) : null,
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
}
