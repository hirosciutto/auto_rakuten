<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('search_conditions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('site_id')->comment('sites.id');
            $table->integer('total_hits')->default(0)->comment('トータルの取得件数');
            $table->string('frequency', 20)->comment('once|daily|weekly|monthly');
            // 検索条件（APIパラメータをスネークケースで記述）
            $table->string('keyword', 256)->nullable();
            $table->unsignedTinyInteger('or_flag')->nullable()->comment('0:AND 1:OR');
            $table->string('ng_keyword', 256)->nullable();

            $table->string('shop_code', 64)->nullable();
            $table->string('item_code', 64)->nullable();
            $table->unsignedBigInteger('genre_id')->nullable();
            $table->string('tag_id', 128)->nullable()->comment('カンマ区切り、最大10ID');
            $table->unsignedTinyInteger('page')->nullable()->comment('1-100');
            $table->unsignedInteger('min_price')->nullable()->comment('0-999999999');
            $table->unsignedInteger('max_price')->nullable()->comment('0-999999999');
            $table->unsignedTinyInteger('availability')->nullable()->comment('0:すべて 1:在庫ありのみ');
            $table->unsignedTinyInteger('purchase_type')->nullable()->comment('0:通常 1:定期購入 2:頒布会');

            $table->tinyInteger('overwrite')->default(0)->comment('0.SKIP(すでに存在するitem_codeがある場合はskipして数える) 1.上書き');
            $table->tinyInteger('is_active')->default(0)->comment('0:無効 1:有効');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_conditions');
    }
};
