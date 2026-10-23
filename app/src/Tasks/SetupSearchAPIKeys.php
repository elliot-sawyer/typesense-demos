<?php
namespace ElliotSawyer\TypesenseSearch\CodeCamp\Tasks;

use Symfony\Component\Console\Input\InputInterface;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Command\Command;
use ElliotSawyer\SilverstripeTypesense\Typesense;
use ElliotSawyer\TypesenseSearch\TypesenseSearchPage;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;

class SetupSearchAPIKeys extends BuildTask
{
    protected string $title = 'Setup Typesense search API keys';

    protected static string $description = 'Creates a search-only Typesense API key for each search page, replacing any existing key.';

    protected static string $commandName = 'SetupSearchAPIKeys';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $client = Typesense::client();
        $existingKeys = $client->keys->retrieve()['keys'] ?? [];
        foreach (TypesenseSearchPage::get() as $page) {
            $collection = $page->Collection();
            if (!$collection || !$collection->exists()) {
                DB::alteration_message("Skipping \"{$page->Title}\" — no collection assigned", 'notice');
                continue;
            }

            $collectionName = $collection->Name;
            $description = "codecamp-search-{$collectionName}";

            // Delete any existing key with this description
            foreach ($existingKeys as $key) {
                if (($key['description'] ?? '') === $description) {
                    $client->keys[$key['id']]->delete();
                    DB::alteration_message("Deleted existing key for \"{$collectionName}\"");
                }
            }

            // Addresses_Codecamp also needs access to the suburbs collection for polyline display
            $collections = $collectionName === 'Addresses_Codecamp'
                ? [$collectionName, 'suburbs']
                : [$collectionName];

            // Create a new search-only key scoped to this collection
            $result = $client->keys->create([
                'description' => $description,
                'actions'     => ['documents:search'],
                'collections' => $collections,
            ]);

            $page->SearchAPIKey = $result['value'];
            $page->write();
            $page->publishSingle();

            DB::alteration_message("Created search key for \"{$page->Title}\" ({$collectionName})");
        }
        return Command::SUCCESS;
    }
}
