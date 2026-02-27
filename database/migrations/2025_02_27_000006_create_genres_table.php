<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 楽天市場ジャンル（親階層 genreLevel=1 の一覧）
     */
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('genre_id')->unique()->comment('楽天ジャンルID');
            $table->string('genre_name')->comment('ジャンル名');
            $table->unsignedTinyInteger('genre_level')->default(1)->comment('ジャンル階層レベル');
            $table->string('english_name', 64)->nullable()->comment('英語名');
            $table->unsignedBigInteger('link_genre_id')->nullable()->comment('リンクジャンルID');
            $table->tinyInteger('chopper_flg')->default(0);
            $table->tinyInteger('lowest_flg')->default(0);
            $table->timestamps();
        });

        $rows = [
            ['genre_id' => 100371, 'genre_name' => 'レディースファッション', 'genre_level' => 1, 'english_name' => 'ladiesfashion'],
            ['genre_id' => 551177, 'genre_name' => 'メンズファッション', 'genre_level' => 1, 'english_name' => 'mensfashion'],
            ['genre_id' => 100433, 'genre_name' => 'インナー・下着・ナイトウェア', 'genre_level' => 1, 'english_name' => 'inner'],
            ['genre_id' => 216131, 'genre_name' => 'バッグ・小物・ブランド雑貨', 'genre_level' => 1, 'english_name' => 'fashiongoods'],
            ['genre_id' => 558885, 'genre_name' => '靴', 'genre_level' => 1, 'english_name' => 'shoes'],
            ['genre_id' => 558929, 'genre_name' => '腕時計', 'genre_level' => 1, 'english_name' => 'watch'],
            ['genre_id' => 216129, 'genre_name' => 'ジュエリー・アクセサリー', 'genre_level' => 1, 'english_name' => 'accessories'],
            ['genre_id' => 100533, 'genre_name' => 'キッズ・ベビー・マタニティ', 'genre_level' => 1, 'english_name' => 'baby'],
            ['genre_id' => 566382, 'genre_name' => 'おもちゃ', 'genre_level' => 1, 'english_name' => 'toy'],
            ['genre_id' => 101070, 'genre_name' => 'スポーツ・アウトドア', 'genre_level' => 1, 'english_name' => 'sports'],
            ['genre_id' => 562637, 'genre_name' => '家電', 'genre_level' => 1, 'english_name' => 'appliance'],
            ['genre_id' => 211742, 'genre_name' => 'TV・オーディオ・カメラ', 'genre_level' => 1, 'english_name' => 'electronics'],
            ['genre_id' => 100026, 'genre_name' => 'パソコン・周辺機器', 'genre_level' => 1, 'english_name' => 'computer'],
            ['genre_id' => 564500, 'genre_name' => 'スマートフォン・タブレット', 'genre_level' => 1, 'english_name' => 'smartdevice'],
            ['genre_id' => 565004, 'genre_name' => '光回線・モバイル通信', 'genre_level' => 1, 'english_name' => 'telecommunication'],
            ['genre_id' => 100227, 'genre_name' => '食品', 'genre_level' => 1, 'english_name' => 'food'],
            ['genre_id' => 551167, 'genre_name' => 'スイーツ・お菓子', 'genre_level' => 1, 'english_name' => 'sweets'],
            ['genre_id' => 100316, 'genre_name' => '水・ソフトドリンク', 'genre_level' => 1, 'english_name' => 'drink'],
            ['genre_id' => 510915, 'genre_name' => 'ビール・洋酒', 'genre_level' => 1, 'english_name' => 'liquor'],
            ['genre_id' => 510901, 'genre_name' => '日本酒・焼酎', 'genre_level' => 1, 'english_name' => 'sake'],
            ['genre_id' => 100804, 'genre_name' => 'インテリア・寝具・収納', 'genre_level' => 1, 'english_name' => 'interior'],
            ['genre_id' => 215783, 'genre_name' => '日用品雑貨・文房具・手芸', 'genre_level' => 1, 'english_name' => 'daily'],
            ['genre_id' => 558944, 'genre_name' => 'キッチン用品・食器・調理器具', 'genre_level' => 1, 'english_name' => 'kitchen'],
            ['genre_id' => 200162, 'genre_name' => '本・雑誌・コミック', 'genre_level' => 1, 'english_name' => 'book'],
            ['genre_id' => 101240, 'genre_name' => 'CD・DVD', 'genre_level' => 1, 'english_name' => 'media'],
            ['genre_id' => 101205, 'genre_name' => 'テレビゲーム', 'genre_level' => 1, 'english_name' => 'game'],
            ['genre_id' => 101164, 'genre_name' => 'ホビー', 'genre_level' => 1, 'english_name' => 'hobby'],
            ['genre_id' => 112493, 'genre_name' => '楽器・音響機器', 'genre_level' => 1, 'english_name' => 'instrument'],
            ['genre_id' => 101114, 'genre_name' => '車・バイク', 'genre_level' => 1, 'english_name' => 'auto'],
            ['genre_id' => 503190, 'genre_name' => '車用品・バイク用品', 'genre_level' => 1, 'english_name' => 'autogoods'],
            ['genre_id' => 100939, 'genre_name' => '美容・コスメ・香水', 'genre_level' => 1, 'english_name' => 'beauty'],
            ['genre_id' => 100938, 'genre_name' => 'ダイエット・健康', 'genre_level' => 1, 'english_name' => 'health'],
            ['genre_id' => 551169, 'genre_name' => '医薬品・コンタクト・介護', 'genre_level' => 1, 'english_name' => 'medicine'],
            ['genre_id' => 101213, 'genre_name' => 'ペット・ペットグッズ', 'genre_level' => 1, 'english_name' => 'pet'],
            ['genre_id' => 100005, 'genre_name' => '花・ガーデン・DIY', 'genre_level' => 1, 'english_name' => 'flower'],
            ['genre_id' => 101438, 'genre_name' => 'サービス・リフォーム', 'genre_level' => 1, 'english_name' => 'service'],
            ['genre_id' => 111427, 'genre_name' => '住宅・不動産', 'genre_level' => 1, 'english_name' => 'realestates'],
            ['genre_id' => 101381, 'genre_name' => 'カタログギフト・チケット', 'genre_level' => 1, 'english_name' => 'ticket'],
            ['genre_id' => 100000, 'genre_name' => '百貨店・総合通販・ギフト', 'genre_level' => 1, 'english_name' => 'department'],
        ];

        $now = now();
        foreach ($rows as $row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        DB::table('genres')->insert($rows);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genres');
    }
};
