<?php
namespace ElliotSawyer\TypesenseSearch\Geospatial;

use ElliotSawyer\SilverstripeTypesense\Typesense;
use Exception;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DB;

class TypesenseSuburbImporter extends BuildTask
{
    private static $segment = 'TypesenseSuburbImporter';

    public $title = 'Build suburb polylines for Typesense';

    private $councils = [];
    private $cities = [];
    private $suburbs = [];
    private $documents = [];

    public function run($request) {
        $this->preprocess();
        $this->importSuburbs();
        $this->deleteTypesenseCollection();
        $this->buildTypesenseCollection();
    }

    public function preprocess()
    {
        $councilsDB = DB::query("SELECT ID, Name FROM NZTerritorialAuthority");
        foreach($councilsDB as $council) {
            $this->councils[$council['ID']] = $council['Name'];
        }

        $citiesDB = DB::query("SELECT ID, Name FROM NZTownCity");
        foreach($citiesDB as $city) {
            $this->cities[$city['ID']] = $city['Name'];
        }

        $suburbsDB = DB::query("SELECT ID, Name FROM NZSuburbLocality");
        foreach($suburbsDB as $suburb) {
            $this->suburbs[$suburb['ID']] = $suburb['Name'];
        }
    }

    public function deleteTypesenseCollection()
    {
        try {
            $client = Typesense::client();
            $client->collections['suburbs']->delete();
        } catch (Exception $e) {}
    }

    public function buildTypesenseCollection()
    {
        try {
            $client = Typesense::client();
            $client->collections->create([
                'name' => 'suburbs',
                'enable_nested_fields' => true,
                'fields' => [
                    [ 'name' => 'council.name', 'type' => 'string' ],
                    [ 'name' => 'city.name', 'type' => 'string' ],
                    [ 'name' => 'suburb.name', 'type' => 'string' ],
                    [ 'name' => 'polyline', 'type' => 'string' ],
                ]
            ]);

            $client->collections['suburbs']->documents->import($this->documents, ['action' => 'emplace']);

        } catch (Exception $e) {}
    }

    public function importSuburbs()
    {

        $suburbsDB = DB::query("SELECT ID, CouncilID, CityID, SuburbID, Polyline FROM NZSuburb");
        foreach($suburbsDB as $suburb) {
            $councilName = $this->councils[$suburb['CouncilID']];
            $cityName = $this->cities[$suburb['CityID']];
            $suburbName = $this->suburbs[$suburb['SuburbID']];
            $polyline = $suburb['Polyline'];

            if($councilName && $cityName && $suburbName && $polyline) {
                $this->documents[] = [
                    'id' => (string) $suburb['ID'],
                    'council.name' => $councilName,
                    'city.name' => $cityName,
                    'suburb.name' => $suburbName,
                    'polyline' => $polyline,
                ];

            }
        }
    }
}
