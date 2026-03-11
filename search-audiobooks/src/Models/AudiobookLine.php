<?php
namespace ElliotSawyer\TypesenseSearch\AudiobookSearch;

use Psr\SimpleCache\CacheInterface;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBInt;
use SilverStripe\ORM\FieldType\DBText;

class AudiobookLine extends DataObject
{
    private static $table_name = 'AudiobookLine';
    private static $has_one = [
        'Parent' => Audiobook::class
    ];

    private static $db = [
        'Start' => DBInt::class,
        'Stop' => DBInt::class,
        'Body' => DBText::class
    ];

    private static $summary_fields = [
        'Start',
        'Stop',
        'Body',
    ];

    public function getTypesenseDocument()
    {
        $book = $this->getAudiobook();
        return [
            'id' => (string) $this->ID,
            'Title' => $book->Title,
            'Author' => $book->Author,
            'Start' => $this->Start,
            'Stop' => $this->Stop,
            'Body' => $this->Body,
            'ClassName' => $this->ClassName,
            'Created' => strtotime($this->Created),
            'LastEdited' => strtotime($this->LastEdited),
        ];
    }

    public function getAudiobook()
    {
        $cache = Injector::inst()->get(CacheInterface::class.'.Audiobook');
        $key = 'Audiobook_'.$this->ParentID;
        if(!($book = $cache->get($key))) {
            $book = $this->Parent();
            $cache->set($key, $book, 3600);
        }

        return $book;
    }

    public function Duration($field = 'Start')
    {
        $milliSeconds = $this->$field;
        $hours = 0;
        $minutes = 0;
        $seconds = 0;
        if($milliSeconds) {
            // Convert Milli Seconds to hours, minutes, and seconds
            $hours = floor($milliSeconds / (60 * 60 * 1000));
            $minutes = floor($milliSeconds / (60 * 1000));
            $seconds = floor(($milliSeconds % (60 * 1000)) / 1000);
        }

        return sprintf(
            "%s:%s:%s",
            str_pad($hours, 2, '0', STR_PAD_LEFT),
            str_pad($minutes, 2, '0', STR_PAD_LEFT),
            str_pad($seconds, 2, '0', STR_PAD_LEFT),
        );
    }
}
