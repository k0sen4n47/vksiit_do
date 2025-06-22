<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_page_id',
        'title',
        'description',
        'time_limit',
        'passing_score',
        'max_attempts',
        'shuffle_questions',
        'show_results',
        'is_active'
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
        'shuffle_questions' => 'boolean',
        'show_results' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Получить вопросы теста.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Получить страницу задания, связанную с этим тестом.
     */
    public function assignmentPage(): HasOne
    {
        return $this->hasOne(AssignmentPage::class);
    }

    /**
     * Получить результаты тестирования.
     */
    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    /**
     * Получить максимально возможный балл за тест.
     */
    public function getMaxScoreAttribute(): int
    {
        return $this->questions()->sum('points');
    }

    /**
     * Получить количество вопросов в тесте.
     */
    public function getQuestionsCountAttribute(): int
    {
        return $this->questions()->count();
    }

    /**
     * Получить количество студентов, прошедших тест.
     */
    public function getCompletedResultsCountAttribute(): int
    {
        return $this->testResults()->where('status', 'completed')->count();
    }

    /**
     * Получить средний балл по тесту.
     */
    public function getAverageScoreAttribute(): float
    {
        $completedResults = $this->testResults()->where('status', 'completed');
        if ($completedResults->count() === 0) {
            return 0;
        }
        return round($completedResults->avg('score'), 2);
    }
}
