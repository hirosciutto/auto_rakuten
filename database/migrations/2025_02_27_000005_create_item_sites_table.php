<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * sites と items の多対多の中間テーブル
     */
    public function up(): void
    {
        Schema::create('item_sites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id')->comment('sites.id');
            $table->unsignedBigInteger('item_id')->comment('items.id');
            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->unique(['site_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sites');
    }
};
