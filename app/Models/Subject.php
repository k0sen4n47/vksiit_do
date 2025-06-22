<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'abbreviation',
        'description',
        'image'
    ];

    /**
     * Получить преподавателей, связанных с предметом.
     *
     * @return BelongsToMany
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subject_teacher_group', 'subject_id', 'user_id')
            ->where('users.role', 'teacher')
            ->withTimestamps();
    }

    /**
     * Получить группы, связанные с предметом.
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'subject_teacher_group', 'subject_id', 'group_id')
            ->withTimestamps();
    }

    /**
     * Получить задания по предмету.
     *
     * @return HasMany
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Получить связи предмета с преподавателями и группами.
     *
     * @return HasMany
     */
    public function teacherGroupConnections(): HasMany
    {
        return $this->hasMany(SubjectTeacherGroup::class);
    }
}
