<?php
namespace ElliotSawyer\TypesenseSearch\ScopedSearch;

use SilverStripe\Core\ClassInfo;

class SharedDocument extends \Page
{
    //solely created so Typesense can index by page type
    private $table_name = 'SharedDocument';
    private $singular_name = 'Shared document';
    private $plural_name = 'Shared documents';

    public function getTypesenseDocument()
    {
        $content = strip_tags((string) $this->Content);
        $content = preg_replace('/\s+/', ' ', $content);
        $link = str_replace('http://', 'https://', $this->Link());

        return [
            'id' => (string) $this->ID,
            'Title' => $this->Title,
            'Content' => $content,
            'Link' => $link,
            'OwnerID' => array_values($this->ViewerMembers()->map('ID', 'ID')->toArray()),
            'ClassName' => ClassInfo::shortName($this->ClassName),
            'Created' => strtotime($this->owner->Created),
            'LastEdited' => strtotime($this->owner->LastEdited),
        ];
    }
}
