<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 楽天APIの sort パラメータ値（仕様 4.3 準拠）
     */
    public function up(): void
    {
        Schema::table('search_conditions', function (Blueprint $table) {
            $table->enum('sort', [
                'standard',
                '+itemPrice',
                '-itemPrice',
                '+reviewCount',
                '-reviewCount',
                '+reviewAverage',
                '-reviewAverage',
                '+affiliateRate',
                '-affiliateRate',
                '+updateTimestamp',
                '-updateTimestamp',
            ])->default('standard')->after('tag_id')
                ->comment('楽天API sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('search_conditions', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
};
