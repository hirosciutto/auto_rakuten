<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * status=99（失敗）時にエラー詳細を保存する。
     */
    public function up(): void
    {
        Schema::table('search_logs', function (Blueprint $table) {
            $table->text('error_message')->nullable()->after('status')->comment('失敗時（status=99）のエラー内容');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('search_logs', function (Blueprint $table) {
            $table->dropColumn('error_message');
        });
    }
};
