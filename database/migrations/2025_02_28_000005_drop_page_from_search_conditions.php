<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * total_hits に基づき page は 1 からループするため、page カラムは不要。
     */
    public function up(): void
    {
        Schema::table('search_conditions', function (Blueprint $table) {
            $table->dropColumn('page');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('search_conditions', function (Blueprint $table) {
            $table->unsignedTinyInteger('page')->nullable()->after('tag_id')->comment('1-100');
        });
    }
};
