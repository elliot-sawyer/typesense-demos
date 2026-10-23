<?php
namespace ElliotSawyer\TypesenseSearch\CodeCamp;

use ElliotSawyer\TypesenseSearch\Resene\ReseneColour;
use SilverStripe\Admin\ModelAdmin;

class CodecampAdmin extends ModelAdmin
{
    private static $url_segment = 'Codecamp';
    private static $menu_title = "Codecamp";
    private static $managed_models = [
        ReseneColour::class,
    ];
}
