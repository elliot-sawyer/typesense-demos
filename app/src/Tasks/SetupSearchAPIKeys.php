<?php
namespace ElliotSawyer\TypesenseSearch\CodeCamp\Tasks;

use ElliotSawyer\SilverstripeTypesense\Typesense;
use ElliotSawyer\TypesenseSearch\TypesenseSearchPage;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;

class SetupSearchAPIKeys extends BuildTask
{
    private static $segment = 'SetupSearchAPIKeys';

    public function getTitle()
    {
        return 'Setup Typesense search API keys';
    }

    public function getDescription()
    {
        return 'Creates a search-only Typesense API key for each search page, replacing any existing key.';
    }

    public function run($request)
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

            // Create a new search-only key scoped to this collection
            $result = $client->keys->create([
                'description' => $description,
                'actions'     => ['documents:search'],
                'collections' => [$collectionName],
            ]);

            $page->SearchAPIKey = $result['value'];
            $page->write();
            $page->publishSingle();

            DB::alteration_message("Created search key for \"{$page->Title}\" ({$collectionName})");
        }
    }
}
