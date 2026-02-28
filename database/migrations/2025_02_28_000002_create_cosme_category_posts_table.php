<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * posts と cosme_categories の多対多中間テーブル
     */
    public function up(): void
    {
        Schema::create('cosme_category_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->comment('posts.id');
            $table->unsignedBigInteger('cosme_category_id')->comment('cosme_categories.id');
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('cosme_category_id')->references('id')->on('cosme_categories')->onDelete('cascade');
            $table->unique(['post_id', 'cosme_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cosme_category_posts');
    }
};
