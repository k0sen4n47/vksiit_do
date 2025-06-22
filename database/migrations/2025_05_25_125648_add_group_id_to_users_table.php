<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запускает миграции.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Добавляем внешний ключ для связи с группой (для студентов)
            $table->foreignId('group_id')->nullable()->constrained('groups')->onDelete('set null');
        });
    }

    /**
     * Откатывает миграции.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем внешний ключ и поле group_id при откате миграции
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
};
