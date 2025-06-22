<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'question_text',
        'type',
        'points'
    ];

    protected $casts = [
        'points' => 'integer'
    ];

    /**
     * Получить тест, к которому относится вопрос.
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Получить ответы на вопрос.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Получить ответы студентов на этот вопрос.
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Получить правильные ответы на вопрос.
     */
    public function correctAnswers(): HasMany
    {
        return $this->hasMany(Answer::class)->where('is_correct', true);
    }

    /**
     * Проверить, является ли вопрос с множественным выбором.
     */
    public function isMultipleChoice(): bool
    {
        return $this->type === 'multiple';
    }

    /**
     * Проверить, является ли вопрос текстовым.
     */
    public function isTextQuestion(): bool
    {
        return $this->type === 'text';
    }

    /**
     * Получить статистику по вопросу.
     */
    public function getStatisticsAttribute(): array
    {
        $totalAnswers = $this->studentAnswers()->count();
        $correctAnswers = $this->studentAnswers()->where('is_correct', true)->count();
        
        return [
            'total_answers' => $totalAnswers,
            'correct_answers' => $correctAnswers,
            'correct_percentage' => $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 2) : 0,
            'average_points' => $totalAnswers > 0 ? round($this->studentAnswers()->avg('points_earned'), 2) : 0
        ];
    }
}
