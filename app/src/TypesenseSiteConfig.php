<?php
namespace ElliotSawyer\TypesenseSearch\CodeCamp;

use ElliotSawyer\SilverstripeTypesense\Collection;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;

class TypesenseSiteConfig extends Extension
{
    private static $db = [
        'TypesenseProtocol' => 'Enum("http,https","https")',
        'TypesenseHostname' => 'Varchar(64)',
        'TypesensePath' => 'Varchar(64)',
        'TypesensePort' => 'Int(8108)',
        'TypesenseSearchKey' => 'Varchar(32)',
        'TypesenseHitsPerPage' => 'Int(12)',
        'TypesenseQueryFields' => 'Varchar(100)'
    ];

    private static $has_one = [
        'TypesenseCollection' => Collection::class,
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields = $fields->addFieldsToTab('Root.Typesense', [
            LiteralField::create('TypesenseNoticeMessage', "
                <div class='message text-center'>
                    These values are used to configure the frontend search application to talk to your Typesense installation.
                </div>
            "),
            FieldGroup::create([
                DropdownField::create('TypesenseProtocol', 'Protocol')
                    ->setSource($this->owner->dbObject('TypesenseProtocol')->enumValues()),
                TextField::create('TypesenseHostname', 'Hostname')
                    ->setAttribute('placeholder', 'search.example.com'),
                TextField::create('TypesensePath', 'Path')
                    ->setAttribute('placeholder', '/typesense (optional)'),
                NumericField::create('TypesensePort', 'Port')
                    ->setAttribute('pattern', "\d*")
                    ->setAttribute('placeholder', "8108")
                    ->setHTML5(true)
            ])->setTitle('Connection Settings'),
            TextField::create('TypesenseSearchKey', 'Search API Key')
                ->setDescription('This value is a search-only API key for the collection hosting your search page. It is generated from the Typesense Dashboard, and is <strong>NOT</strong> your admin root key!'),
            NumericField::create('TypesenseHitsPerPage', 'Hits per page')
                ->setDescription('Number of hits per page to display'),
            TextField::create('TypesenseQueryFields', 'Query by fields')
                ->setDescription('Comma seperated fields, should match the Typesense collection'),
        ]);

        $collections = Collection::get()->sort('Name ASC');
        if($collections) {
            $fields->insertAfter(
                'TypesenseSearchKey',
                DropdownField::create(
                    'TypesenseCollectionID',
                    'Collection',
                    $collections
                )->setEmptyString('(Select one)')
            );
        }
    }

    public function onBeforeWrite()
    {
        $path = $this->owner->TypesensePath;
        if($path) {
            //strip slashes from ends of user-input
            $path = trim($path, '/');

            //path only supports slash at beginning, no trailing slash
            $this->owner->TypesensePath = '/' . $path;
        } else {
            $this->owner->TypesensePath = null;
        }
    }

}
