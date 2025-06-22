<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_result_id',
        'question_id',
        'answer_text',
        'selected_answers',
        'points_earned',
        'is_correct',
        'teacher_comment',
        'answered_at'
    ];

    protected $casts = [
        'selected_answers' => 'array',
        'points_earned' => 'integer',
        'is_correct' => 'boolean',
        'answered_at' => 'datetime'
    ];

    /**
     * Получить результат теста, к которому относится ответ.
     */
    public function testResult(): BelongsTo
    {
        return $this->belongsTo(TestResult::class);
    }

    /**
     * Получить вопрос, на который дан ответ.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Получить выбранные варианты ответов (для закрытых вопросов).
     */
    public function getSelectedAnswersAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Установить выбранные варианты ответов.
     */
    public function setSelectedAnswersAttribute($value)
    {
        $this->attributes['selected_answers'] = json_encode($value ?? []);
    }

    /**
     * Проверить, является ли ответ текстовым.
     */
    public function isTextAnswer(): bool
    {
        return !empty($this->answer_text);
    }

    /**
     * Проверить, является ли ответ множественным выбором.
     */
    public function isMultipleChoice(): bool
    {
        return !empty($this->selected_answers) && count($this->selected_answers) > 1;
    }

    /**
     * Получить текст ответа для отображения.
     */
    public function getDisplayAnswerAttribute(): string
    {
        if ($this->isTextAnswer()) {
            return $this->answer_text;
        }

        if (!empty($this->selected_answers)) {
            $answers = Answer::whereIn('id', $this->selected_answers)->pluck('answer_text');
            return $answers->implode(', ');
        }

        return 'Нет ответа';
    }
}
