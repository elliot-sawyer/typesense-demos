<?php
namespace ElliotSawyer\TypesenseSearch;

use ElliotSawyer\SilverstripeTypesense\Typesense;
use PageController;
use SilverStripe\Core\Environment;
use SilverStripe\View\Requirements;

class TypesenseSearchPageController extends PageController
{
    private static $alternate_typesense_host = null;
    private static $instantsearch_cdn = 'https://cdn.jsdelivr.net/npm/instantsearch.js@4';
    private static $instantsearch_typesense_cdn = 'https://cdn.jsdelivr.net/npm/typesense-instantsearch-adapter@3/dist/typesense-instantsearch-adapter.min.js';
    public function init()
    {
        parent::init();
        $script = '
        String.prototype.toHHMMSS = function () {
            var sec_num = parseInt(this, 10); // don\'t forget the second param
            sec_num /= 1000;
            var hours   = Math.floor(sec_num / 3600);
            var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
            var seconds = Math.floor(sec_num - (hours * 3600) - (minutes * 60));

            if (hours   < 10) {hours   = "0"+hours;}
            if (minutes < 10) {minutes = "0"+minutes;}
            if (seconds < 10) {seconds = "0"+seconds;}
            return hours+":"+minutes+":"+seconds;
        }
        ';
        Requirements::customScript($script);
        Requirements::javascript($this->config()->get('instantsearch_cdn'));
        Requirements::javascript($this->config()->get('instantsearch_typesense_cdn'));

    }

    //avoid hardcoding this
    public function Host()
    {
        return $this->config()->get('alternate_typesense_host')
            ?? Typesense::parse_typesense_server()['host']
            ?? '';
    }

    public function Port()
    {
        return Typesense::parse_typesense_server()['port'] ?? '';
    }

    public function Protocol()
    {
        return Typesense::parse_typesense_server()['scheme'] ?? '';
    }

    public function SearchAPIKey()
    {
        return $this->data()->SearchAPIKey
            ?: Environment::getEnv('TYPESENSE_SEARCH_KEY');
    }

}
