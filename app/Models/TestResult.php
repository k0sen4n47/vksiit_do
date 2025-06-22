<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'student_id',
        'score',
        'max_score',
        'started_at',
        'completed_at',
        'status',
        'answers_data',
        'teacher_comment'
    ];

    protected $casts = [
        'score' => 'integer',
        'max_score' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'answered_at' => 'datetime',
        'answers_data' => 'array',
        'is_correct' => 'boolean'
    ];

    /**
     * Получить тест, к которому относится результат.
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Получить студента, который проходил тест.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Получить ответы студента на вопросы.
     */
    public function studentAnswers(): HasMany
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Получить процент выполнения теста.
     */
    public function getPercentageAttribute(): float
    {
        if ($this->max_score === 0) {
            return 0;
        }
        return round(($this->score / $this->max_score) * 100, 2);
    }

    /**
     * Проверить, завершён ли тест.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Проверить, истёк ли срок теста.
     */
    public function isTimeout(): bool
    {
        return $this->status === 'timeout';
    }

    /**
     * Получить время выполнения теста в минутах.
     */
    public function getDurationInMinutes(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }
        return $this->started_at->diffInMinutes($this->completed_at);
    }
}
