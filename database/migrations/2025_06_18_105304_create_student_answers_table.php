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
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_result_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('answer_text')->nullable(); // Текстовый ответ (для открытых вопросов)
            $table->json('selected_answers')->nullable(); // ID выбранных ответов (для закрытых вопросов)
            $table->integer('points_earned')->default(0); // Полученные баллы за вопрос
            $table->boolean('is_correct')->default(false); // Правильность ответа
            $table->text('teacher_comment')->nullable(); // Комментарий преподавателя
            $table->timestamp('answered_at')->nullable(); // Время ответа
            $table->timestamps();

            // Уникальный индекс: один ответ на вопрос в рамках одного результата теста
            $table->unique(['test_result_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
