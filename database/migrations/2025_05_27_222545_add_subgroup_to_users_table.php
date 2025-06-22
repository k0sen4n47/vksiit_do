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
        Schema::table('users', function (Blueprint $table) {
            // Добавляем новое поле 'subgroup' типа string, которое может быть null
            // Указываем after('group_id'), чтобы поле добавилось после group_id (опционально, для порядка)
            $table->string('subgroup')->nullable()->after('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем поле 'subgroup' при откате миграции
            $table->dropColumn('subgroup');
        });
    }
};
