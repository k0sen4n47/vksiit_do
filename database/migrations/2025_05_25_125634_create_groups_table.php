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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Полное название группы (ИСП-321 Р)
            $table->string('short_name'); // Сокращенное название (ИСП)
            $table->integer('course'); // Курс (3)
            $table->integer('year'); // Год поступления (21)
            $table->string('suffix')->nullable(); // Подразделение/суффикс (Р)
            // Внешний ключ для связи с куратором (пользователь с ролью преподаватель)
            $table->foreignId('curator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Откатывает миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
