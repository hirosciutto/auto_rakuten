<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 楽天API 商品検索レスポンスのショップ情報を保存
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_code', 64)->unique()->comment('楽天ショップコード（URLの[xyz]部分）');
            $table->string('shop_name')->nullable();
            $table->string('shop_url', 512)->nullable();
            $table->string('shop_affiliate_url', 512)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
