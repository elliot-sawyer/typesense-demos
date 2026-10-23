<?php
namespace ElliotSawyer\TypesenseSearch\AudiobookSearch;

use ElliotSawyer\TypesenseSearch\TypesenseSearchPage;
use SilverShop\HasOneField\HasOneButtonField;

class SearchableAudiobookPage extends TypesenseSearchPage
{
    private static $table_name = 'SearchableAudiobook';
    private static $singular_name = 'Searchable audiobook page';
    private static $plural_name = 'Searchable audiobook pages';
    private static $cms_icon_class = 'font-icon-block-microphone';
    private static $has_one = [
        'Audiobook' => Audiobook::class
    ];
    private static $owns = ['Audiobook'];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->insertBefore('ElementalArea', HasOneButtonField::create($this, 'Audiobook'));

        return $fields;
    }
}
