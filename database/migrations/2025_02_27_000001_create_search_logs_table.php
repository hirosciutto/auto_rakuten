<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('search_condition_id')->comment('search_conditions.id');
            $table->string('frequency', 20)->comment('once|daily|weekly|monthly');
            $table->integer('status')->comment('0:待機中 1:実行中 2:成功 99:失敗');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
