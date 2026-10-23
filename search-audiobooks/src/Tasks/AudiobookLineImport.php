<?php
namespace ElliotSawyer\TypesenseSearch\AudiobookSearch;

use Symfony\Component\Console\Input\InputInterface;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Command\Command;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;

class AudiobookLineImport extends BuildTask
{
    protected static string $commandName = 'AudiobookLineImport';

    protected string $title = 'Audiobook transcript import';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $books = Audiobook::get();
        foreach($books as $book) {
            DB::alteration_message('Importing audiobook "' . $book->Title . '"');
            $book->importLines();
        }
        return Command::SUCCESS;
    }
}
