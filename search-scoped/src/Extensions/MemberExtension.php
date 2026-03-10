<?php
namespace ElliotSawyer\TypesenseSearch\ScopedSearch;

use SilverStripe\Core\Extension;

class MemberExtension extends Extension
{
    private static $db = [
        'TypesenseAPISearchKey' => 'Varchar(255)',
        'TypesenseAPISearchKeyExpires' => 'Datetime',
    ];
}
