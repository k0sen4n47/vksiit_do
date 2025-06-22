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
            // Добавляем поле для роли пользователя. По умолчанию 'student'.
            $table->string('role')->default('student')->after('password');
        });
    }

    /**
     * Откатывает миграции.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем поле role при откате миграции
            $table->dropColumn('role');
        });
    }
};
