<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * コスメティカサイト用のカテゴリ（サイト独自のジャンル分け）
     */
    public function up(): void
    {
        Schema::create('cosme_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('表示名');
            $table->string('slug', 64)->unique()->comment('URL・識別用');
            $table->string('type', 20)->default('category')->comment('category: カテゴリグリッド / mood: ムードフィルター');
            $table->unsignedSmallInteger('sort_order')->default(0)->comment('表示順');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cosme_categories');
    }
};
