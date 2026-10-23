<?php
namespace ElliotSawyer\TypesenseSearch\Resene;

use Symfony\Component\Console\Input\InputInterface;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Command\Command;
use League\Csv\Reader;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;
use SilverStripe\Taxonomy\TaxonomyTerm;

class ReseneColourImport extends BuildTask
{
    protected static string $commandName = 'ReseneColourImport';

    protected string $title = 'Resene colour import';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $csv = Reader::createFromPath(BASE_PATH . '/' . 'resene.csv');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();
        $reseneColoursParentTag = TaxonomyTerm::get()->find('Name', 'Resene Colour Charts')
            ?: TaxonomyTerm::create(['Name' => 'Resene Colour Charts']);
        if(!($reseneColoursParentTag && $reseneColoursParentTag->ID)) {
            $reseneColoursParentTag->write();
        }
        foreach($records as $colour) {
            if(!$colour['Code']) continue;
            $chart = $colour['Chart'];
            unset($colour['Chart']);

            $resene = ReseneColour::get()->find('Code', $colour['Code'])
                ?: ReseneColour::create($colour);

            if(!$resene?->ID) $resene->write();

            $chartTag = TaxonomyTerm::get()->find('Name', $chart)
                ?: TaxonomyTerm::create(['Name' => $chart]);

            $chartTag->ParentID = $reseneColoursParentTag->ID;
            if(!$chartTag?->ID) $chartTag->write();
            $resene->Charts()->add($chartTag);
            DB::alteration_message($colour['Name']);
        }
        return Command::SUCCESS;
    }
}
