<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * item_cosme_categories を削除し、cosme_category_posts に移行済みとする。
     */
    public function up(): void
    {
        Schema::dropIfExists('item_cosme_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('item_cosme_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->comment('items.id');
            $table->unsignedBigInteger('cosme_category_id')->comment('cosme_categories.id');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('cosme_category_id')->references('id')->on('cosme_categories')->onDelete('cascade');
            $table->unique(['item_id', 'cosme_category_id']);
        });
    }
};
