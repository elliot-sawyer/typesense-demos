<?php
namespace ElliotSawyer\TypesenseSearch\AudiobookSearch;

use Exception;
use Psr\Log\LoggerInterface;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

class Audiobook extends DataObject
{
    private static $table_name = 'Audiobook';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Author' => 'Varchar(255)',
        'SourceURL' => 'Varchar(255)',
    ];

    private static $summary_fields = [
        'Title',
        'Author',
    ];

    private static $has_one = [
        'AudiobookFile' => File::class,
        'Transcript' => File::class,
        'Cover' => Image::class,
    ];

    private static $has_many = [
        'Lines' => AudiobookLine::class
    ];

    private static $owns = ['Transcript', 'AudiobookFile', 'Cover'];

    public function importLines()
    {
        try {
            if(!$this->ID) throw new Exception('Audiobook has not been saved yet');
            if(!$this->TranscriptID) throw new Exception('Missing transcript for Audiobook #' . $this->ID);

            $book = $this;
            $lines = (string) $this->Transcript()->getString();
            if(!$lines) throw new Exception('Unable to import lines from Transcript #' . $this->TranscriptID);
            $lines = preg_split("/\r?\n/", $lines);
            if(!(is_array($lines) && count($lines) > 1) ) throw new Exception('Unable to parse Transcript #' . $this->TranscriptID);

            //skip the header row
            array_shift($lines);
            $lineCount = count($lines) + 2;

            $linesMap = $this->Lines()->map('Start', 'Stop')->toArray();
            foreach($lines as $i => $line) {
                $line = trim($line);
                @[$start, $stop, $body] = preg_split('/\t/', $line) ?: [0,0,0];
                if(!($start && $stop && $body)) continue;

                if($i % 1000 == 0) {
                    DB::alteration_message("... processed #$i /~ $lineCount");
                }
                $timeFind = [
                    'Start' => $start,
                    'Stop' => $stop,
                ];
                if(isset($linesMap[$start]) && $linesMap[$start] == $stop) continue;
                $line = AudiobookLine::get()->filter($timeFind)->first()
                    ?: AudiobookLine::create([
                        'Start' => $start,
                        'Stop' => $stop,
                        'Body' => $body,
                        //todo: string replacements using CMS data
                        // 'Body' => str_ireplace($needles, $replacements, $body),
                    ]);
                if(!($line?->ID)) {
                    $book->Lines()->add($line);
                }
            }



        } catch(Exception $e) {
            Injector::inst()->get(LoggerInterface::class)->warning($e->getMessage());
            DB::alteration_message($e->getMessage());
        }
    }

}
