<?php
namespace ElliotSawyer\TypesenseSearch\AudiobookSearch;

use League\Csv\Reader;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;

class AudiobookLineImport extends BuildTask
{
    private static $segment = 'AudiobookLineImport';

    public $title = 'Audiobook transcript import';

    public function run($request) {
        $books = Audiobook::get();

        foreach($books as $book) {
            DB::alteration_message('Importing audiobook "' . $book->Title . '"');
            $book->importLines();
        }
    }
}
