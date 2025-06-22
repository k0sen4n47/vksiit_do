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
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->integer('score')->default(0); // Набранные баллы
            $table->integer('max_score')->default(0); // Максимально возможные баллы
            $table->timestamp('started_at')->nullable(); // Время начала теста
            $table->timestamp('completed_at')->nullable(); // Время завершения теста
            $table->enum('status', ['in_progress', 'completed', 'timeout'])->default('in_progress');
            $table->json('answers_data')->nullable(); // Дополнительные данные об ответах
            $table->text('teacher_comment')->nullable(); // Комментарий преподавателя
            $table->timestamps();

            // Уникальный индекс: один студент может проходить тест только один раз
            $table->unique(['test_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
