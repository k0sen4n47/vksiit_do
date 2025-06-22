<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectTeacherGroup extends Model
{
    /**
     * Имя таблицы в базе данных.
     *
     * @var string
     */
    protected $table = 'subject_teacher_group';

    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array
     */
    protected $fillable = [
        'subject_id',
        'user_id',
        'group_id'
    ];

    /**
     * Получить предмет, связанный с этой записью.
     *
     * @return BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Получить преподавателя, связанного с этой записью.
     *
     * @return BelongsTo
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Получить группу, связанную с этой записью.
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
} 