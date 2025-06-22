<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'answer_text',
        'is_correct'
    ];

    protected $casts = [
        'is_correct' => 'boolean'
    ];

    /**
     * Получить вопрос, к которому относится ответ.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
