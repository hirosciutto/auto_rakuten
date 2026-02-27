<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 楽天API 商品検索レスポンスの商品情報を保存
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->comment('shops.id');
            $table->string('item_code', 64)->unique()->comment('API itemCode（例: shop:1234）');

            $table->string('item_name', 512)->nullable();
            $table->string('catchcopy', 512)->nullable();
            $table->unsignedInteger('item_price')->nullable();
            $table->text('item_caption')->nullable();
            $table->string('item_url', 512)->nullable();
            $table->string('affiliate_url', 512)->nullable();

            $table->string('item_price_base_field', 32)->nullable();
            $table->unsignedInteger('item_price_max1')->nullable();
            $table->unsignedInteger('item_price_max2')->nullable();
            $table->unsignedInteger('item_price_max3')->nullable();
            $table->unsignedInteger('item_price_min1')->nullable();
            $table->unsignedInteger('item_price_min2')->nullable();
            $table->unsignedInteger('item_price_min3')->nullable();

            $table->unsignedTinyInteger('image_flag')->nullable()->comment('0:画像なし 1:画像あり');
            $table->json('small_image_urls')->nullable()->comment('64x64、最大3件');
            $table->json('medium_image_urls')->nullable()->comment('128x128、最大3件');

            $table->unsignedTinyInteger('availability')->nullable()->comment('0:在庫切れ 1:在庫あり');
            $table->unsignedTinyInteger('tax_flag')->nullable()->comment('0:税込 1:税抜');
            $table->unsignedTinyInteger('postage_flag')->nullable()->comment('0:送料込み 1:送料別');
            $table->unsignedTinyInteger('credit_card_flag')->nullable()->comment('0:不可 1:可');
            $table->unsignedTinyInteger('shop_of_the_year_flag')->nullable()->comment('0:未受賞 1:受賞');
            $table->unsignedTinyInteger('ship_overseas_flag')->nullable()->comment('0:不可 1:可');
            $table->string('ship_overseas_area', 256)->nullable();
            $table->unsignedTinyInteger('asuraku_flag')->nullable()->comment('0:対象外 1:あす楽可');
            $table->string('asuraku_closing_time', 8)->nullable()->comment('HH:MM');
            $table->string('asuraku_area', 256)->nullable();

            $table->decimal('affiliate_rate', 5, 2)->nullable();
            $table->string('start_time', 32)->nullable()->comment('YYYY-MM-DD HH:MM');
            $table->string('end_time', 32)->nullable()->comment('YYYY-MM-DD HH:MM');
            $table->unsignedInteger('review_count')->nullable();
            $table->decimal('review_average', 3, 2)->nullable();
            $table->unsignedTinyInteger('point_rate')->nullable();
            $table->string('point_rate_start_time', 32)->nullable();
            $table->string('point_rate_end_time', 32)->nullable();
            $table->unsignedTinyInteger('gift_flag')->nullable()->comment('0:不可 1:可');

            $table->unsignedBigInteger('genre_id')->nullable();
            $table->json('tag_ids')->nullable()->comment('タグID配列');

            $table->timestamps();

            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
