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
            // Добавляем поле для логина пользователя. Должно быть уникальным.
            $table->string('login')->unique()->after('email');
        });
    }

    /**
     * Откатывает миграции.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Удаляем поле login при откате миграции
            $table->dropColumn('login');
        });
    }
};
