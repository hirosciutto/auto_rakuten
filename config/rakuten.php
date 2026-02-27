<?php

return [
    'application_id' => env('APP_ID'),
    'access_key' => env('ACCESS_KEY'),
    'affiliate_id' => env('AFFILIATE_ID'),
    'item_search' => [
        'endpoint' => 'https://openapi.rakuten.co.jp/ichibams/api/IchibaItem/Search/20220601',
        'format_version' => 2,
        'default_hits' => 30,
        'default_page' => 100,
    ],
];
