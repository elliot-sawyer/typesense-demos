<?php
namespace ElliotSawyer\TypesenseSearch\Resene;

use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\View\Parsers\URLSegmentFilter;

class ReseneColour extends DataObject
{
    private static $table_name = 'ReseneColour';

    private static $colours = [
        'red' => [255, 0, 0],
        'green' => [0, 255, 0],
        'blue' => [0, 0, 255],
        'cyan' => [0, 255, 255],
        'magenta' => [255, 0, 255],
        'yellow' => [255, 255, 0],
    ];

    private static $thresholds = [
        'grey' => 5,
        'white' => 200,
        'black' => 55,
        'brown' => 100,
    ];

    private static $db = [
        'Name' => 'Varchar(255)',
        'Code' => 'Varchar(32)',
        'R' => 'Int',
        'G' => 'Int',
        'B' => 'Int',
    ];

    private static $many_many = [
        'Charts' => TaxonomyTerm::class
    ];

    private static $summary_fields = [
        'Name',
        'Code',
        'R' => 'Red',
        'G' => 'Green',
        'B' => 'Blue',
        'showHex' => 'Hex',
        'Categorize' => 'Category'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->insertAfter('B',
            LiteralField::create(
                'ShowRGBColour',
                '<div class="w-100 d-block my-4 p-4 h1 display-1" style="background-color:'.$this->Hex().'">&nbsp;</div>'
            )
        );

        $fields->insertAfter('B',
            ReadonlyField::create(
                'ShowCSSVariableName',
                'CSS Variable',
                $this->CSSVariableName()
            )
        );
        return $fields;

    }

    public function Hex()
    {
        $r = str_pad(dechex($this->R), 2, '0', STR_PAD_LEFT);
        $g = str_pad(dechex($this->G), 2, '0', STR_PAD_LEFT);
        $b = str_pad(dechex($this->B), 2, '0', STR_PAD_LEFT);

        return "#$r$g$b";
    }

    public function showHex() {
        return DBField::create_field('HTMLFragment', sprintf("<div style='background-color:%s;height: 32px; width; 64px'>&nbsp;</div>", $this->Hex()));
    }

    public function Categorize()
    {
        $r = $this->R;
        $g = $this->G;
        $b = $this->B;
        // Define the named colors and their RGB values
        $colors = $this->config()->get('colours');

        // Calculate the Euclidean distance for each named color
        $closestColor = null;
        $closestDistance = PHP_INT_MAX;

        foreach ($colors as $colorName => $colorValues) {
            $distance = sqrt(
                pow($r - $colorValues[0], 2) +
                pow($g - $colorValues[1], 2) +
                pow($b - $colorValues[2], 2)
            );

            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closestColor = $colorName;
            }
        }

        // Check if it's a shade of gray
        $greyThreshold = $this->config()->get('thresholds')['grey'];
        $whiteThreshold = $this->config()->get('thresholds')['white'];
        $blackThreshold = $this->config()->get('thresholds')['black'];
        $brownThreshold = $this->config()->get('thresholds')['brown'];
        if (abs($r - $g) <= $greyThreshold && abs($r - $b) <= $greyThreshold && abs($g - $b) <= $greyThreshold) {
            return 'grey';
        }

        if ($r >= $whiteThreshold && $g >= $whiteThreshold && $b >= $whiteThreshold) {
            return 'white';
        }

        if ($r <= $blackThreshold && $g <= $blackThreshold && $b <= $blackThreshold) {
            return 'black';
        }

        if ($r > $b && $g > $b && ($r - $b) >= $brownThreshold && ($g - $b) >= $brownThreshold) {
            return 'brown';
        }

        return $closestColor;
    }

    public function CSSVariableName()
    {
        $variableName = strtolower(URLSegmentFilter::create()->filter($this->Name));
        return sprintf("resene-%s: rgba(%d,%d,%d,1);", $variableName, $this->R, $this->G, $this->B);
    }

    public function getTypesenseDocument()
    {
        return [
            'id' => (string) $this->ID,
            'Name' => $this->Name,
            'Code' => $this->Code,
            'Charts' => array_values($this->Charts()->map('ID', 'Title')->toArray()),
            'Red' => $this->R,
            'Green' => $this->G,
            'Blue' => $this->B,
            'Hex' => $this->Hex(),
            'Category' => $this->Categorize(),
            'ClassName' => $this->ClassName,
            'Created' => strtotime($this->Created),
            'LastEdited' => strtotime($this->LastEdited),
            'rgb_vector' => $this->RGBToVector()
        ];
    }

    public function RGBToVector()
    {
        return [
            round($this->R / 255, 6),
            round($this->G / 255, 6),
            round($this->B / 255, 6),
        ];
    }
}
