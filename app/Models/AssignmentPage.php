<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentPage extends Model
{
    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array
     */
    protected $fillable = [
        'assignment_id',
        'title',
        'content',
        'type',
        'order',
        'test_id'
    ];

    /**
     * Атрибуты, которые должны быть преобразованы.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
        'order' => 'integer'
    ];

    /**
     * Получить задание, к которому относится страница.
     *
     * @return BelongsTo
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Получить тест, связанный со страницей (если тип страницы - test).
     *
     * @return BelongsTo
     */
    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    /**
     * Получить текст страницы (для текстовых страниц)
     */
    public function getTextContent(): string
    {
        return $this->content['text'] ?? '';
    }

    /**
     * Получить код страницы (для страниц с кодом)
     */
    public function getCodeContent(): string
    {
        return $this->content['initial_code'] ?? '';
    }

    /**
     * Получить язык программирования (для страниц с кодом)
     */
    public function getCodeLanguage(): string
    {
        return $this->content['language'] ?? 'javascript';
    }

    /**
     * Получить заголовок страницы
     */
    public function getPageTitle(): string
    {
        return $this->content['title'] ?? $this->title ?? '';
    }
} 