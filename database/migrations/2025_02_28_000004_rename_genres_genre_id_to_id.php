<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 既存の genres テーブル: 旧 id を削除し、genre_id を id にリネームして主キーにする。
     */
    public function up(): void
    {
        // MySQL: AUTO_INCREMENT 付きの主キーはそのまま drop できないため、先に AUTO_INCREMENT を外す
        DB::statement('ALTER TABLE genres MODIFY id BIGINT UNSIGNED NOT NULL');

        Schema::table('genres', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });
        Schema::table('genres', function (Blueprint $table) {
            $table->dropColumn('id');
        });
        Schema::table('genres', function (Blueprint $table) {
            $table->renameColumn('genre_id', 'id');
        });
        Schema::table('genres', function (Blueprint $table) {
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('genres', function (Blueprint $table) {
            $table->dropPrimary(['id']);
        });
        Schema::table('genres', function (Blueprint $table) {
            $table->renameColumn('id', 'genre_id');
        });
        Schema::table('genres', function (Blueprint $table) {
            $table->id()->first();
        });
        Schema::table('genres', function (Blueprint $table) {
            $table->primary('id');
            $table->unique('genre_id');
        });
    }
};
