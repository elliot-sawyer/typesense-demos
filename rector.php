<?php

use Rector\Config\RectorConfig;
use Netwerkstatt\SilverstripeRector\Set\SilverstripeLevelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app/src',
        __DIR__ . '/search-audiobooks/src',
        __DIR__ . '/search-geospatial/src',
        __DIR__ . '/search-resene/src',
        __DIR__ . '/search-scoped/src',
        __DIR__ . '/silverstripe-typesense-instantsearch/src',
        __DIR__ . '/silverstripe-typesense-instantsearch/_config.php',
    ])
    ->withSets([SilverstripeLevelSetList::UP_TO_SS_6_2]);
