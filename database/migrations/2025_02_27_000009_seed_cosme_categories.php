<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();
        $rows = [
            ['name' => 'トランスペアレント', 'slug' => 'transparent', 'type' => 'mood', 'sort_order' => 1],
            ['name' => 'グロー', 'slug' => 'glow', 'type' => 'mood', 'sort_order' => 2],
            ['name' => 'マット', 'slug' => 'matte', 'type' => 'mood', 'sort_order' => 3],
            ['name' => 'ナチュラル', 'slug' => 'natural', 'type' => 'mood', 'sort_order' => 4],
            ['name' => 'マチュア', 'slug' => 'mature', 'type' => 'mood', 'sort_order' => 5],
            ['name' => '韓国スタイル', 'slug' => 'korean-style', 'type' => 'mood', 'sort_order' => 6],
            ['name' => 'スキンケア', 'slug' => 'skincare', 'type' => 'category', 'sort_order' => 10],
            ['name' => 'メイクアップ', 'slug' => 'makeup', 'type' => 'category', 'sort_order' => 11],
            ['name' => 'ベースメイク', 'slug' => 'base-makeup', 'type' => 'category', 'sort_order' => 12],
            ['name' => 'リップ', 'slug' => 'lip', 'type' => 'category', 'sort_order' => 13],
            ['name' => 'アイメイク', 'slug' => 'eye-makeup', 'type' => 'category', 'sort_order' => 14],
            ['name' => 'ネイル', 'slug' => 'nail', 'type' => 'category', 'sort_order' => 15],
        ];
        foreach ($rows as $row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        DB::table('cosme_categories')->insert($rows);
    }

    /**
     * Reverse the migrations.
     * ロールバック時は cosme_category_posts / item_cosme_categories を先に削除すること。
     */
    public function down(): void
    {
        DB::table('cosme_categories')->whereIn('slug', [
            'transparent', 'glow', 'matte', 'natural', 'mature', 'korean-style',
            'skincare', 'makeup', 'base-makeup', 'lip', 'eye-makeup', 'nail',
        ])->delete();
    }
};
