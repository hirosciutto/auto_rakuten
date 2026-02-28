<?php

return [
    /*
    | コスメティカフロントで表示するサイトを site_id で指定する。
    | sites テーブルでコスメティカ用のサイトを登録し、その ID を .env に設定する。
    */
    'site_id' => (int) env('COSMETICA_SITE_ID', 1),
];
