<?php
namespace ElliotSawyer\TypesenseSearch\Geospatial;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extension;

class NZStreetExtension extends Extension
{
    private static $db = [
        'AddressNumberPrefix' =>  'Varchar(100)',
        'TerritorialAuthority' =>  'Varchar(64)',
    ];

    public function getTypesenseDocument()
    {
        $regions = [
            "Ashburton District" => "Canterbury",
            "Auckland" => "Auckland",
            "Buller District" => "West Coast",
            "Carterton District" => "Wellington",
            "Central Hawke's Bay District" => "Hawke's Bay",
            "Central Otago District" => "Otago",
            "Christchurch City" => "Canterbury",
            "Clutha District" => "Otago",
            "Dunedin City" => "Otago",
            "Far North District" => "Northland",
            "Gisborne District" => "Gisborne",
            "Gore District" => "Southland",
            "Grey District" => "West Coast",
            "Hamilton City" => "Waikato",
            "Hastings District" => "Hawke's Bay",
            "Hauraki District" => "Waikato",
            "Horowhenua District" => "Manawatu - Whanganui",
            "Hurunui District" => "Canterbury",
            "Invercargill City" => "Southland",
            "Kaikoura District" => "Canterbury",
            "Kaipara District" => "Northland",
            "Kapiti Coast District" => "Wellington",
            "Kawerau District" => "Bay of Plenty",
            "Lower Hutt City" => "Wellington",
            "Mackenzie District" => "Canterbury",
            "Manawatu District" => "Manawatu - Whanganui",
            "Marlborough District" => "Marlborough",
            "Masterton District" => "Wellington",
            "Matamata-Piako District" => "Waikato",
            "Napier City" => "Hawke's Bay",
            "Nelson City" => "Nelson",
            "New Plymouth District" => "Taranaki",
            "Ōpōtiki District" => "Bay of Plenty",
            "Ōtorohanga District" => "Waikato",
            "Palmerston North City" => "Manawatu - Whanganui",
            "Porirua City" => "Wellington",
            "Queenstown-Lakes District" => "Otago",
            "Rangitikei District" => "Manawatu - Whanganui",
            "Rotorua District" => "Bay of Plenty",
            "Ruapehu District" => "Manawatu - Whanganui",
            "Selwyn District" => "Canterbury",
            "South Taranaki District" => "Taranaki",
            "South Waikato District" => "Waikato",
            "South Wairarapa District" => "Wellington",
            "Southland District" => "Southland",
            "Stratford District" => "Taranaki",
            "Tararua District" => "Manawatu - Whanganui",
            "Tasman District" => "Tasman",
            "Taupo District" => "Waikato",
            "Tauranga City" => "Bay of Plenty",
            "Thames-Coromandel District" => "Waikato",
            "Timaru District" => "Canterbury",
            "Upper Hutt City" => "Wellington",
            "Waikato District" => "Waikato",
            "Waimakariri District" => "Canterbury",
            "Waimate District" => "Canterbury",
            "Waipa District" => "Waikato",
            "Wairoa District" => "Hawke's Bay",
            "Waitaki District" => "Otago",
            "Waitomo District" => "Waikato",
            "Wellington City" => "Wellington",
            "Western Bay of Plenty District" => "Bay of Plenty",
            "Westland District" => "West Coast",
            "Whakatane District" => "Bay of Plenty",
            "Whanganui District" => "Manawatu - Whanganui",
            "Whangarei District" => "Northland",
        ];

        $invalid = [];
        $addr = [];
        if($this->owner->TerritorialAuthority && $regions[$this->owner->TerritorialAuthority] ?? false) {
            $addr =  [
                'id' => (string) $this->owner->AddressID,
                'Region' => $regions[$this->owner->TerritorialAuthority],
                'Council' => $this->owner->TerritorialAuthority,
                'City' => $this->owner->TownCity,
                'Suburb' => $this->owner->SuburbLocality,
                'FullAddress' => $this->owner->FullAddress,
                'Location' => [
                    (float) round($this->owner->Latitude, 6),
                    (float) round($this->owner->Longitude, 6)
                ],
                'ClassName' => $this->owner->ClassName,
                'Created' => strtotime($this->owner->Created),
                'LastEdited' => strtotime($this->owner->LastEdited),
            ];
        } else {
            $invalid[] = $this->owner->FullAddress;
        }

        return $addr;
    }
}
