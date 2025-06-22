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
        Schema::create('subject_teacher_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->timestamps(); // Добавляет created_at и updated_at

            // Уникальный индекс для предотвращения дублирования связей
            $table->unique(['subject_id', 'user_id', 'group_id']);
        });
    }

    /**
     * Откатывает миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_teacher_group');
    }
}; 