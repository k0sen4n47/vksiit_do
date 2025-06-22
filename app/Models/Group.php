<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    /**
     * Атрибуты, которые можно массово присваивать.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'short_name',
        'course',
        'year',
        'suffix',
        'curator_id',
        'name_component_id'
    ];

    /**
     * Атрибуты, которые должны быть преобразованы.
     *
     * @var array
     */
    protected $casts = [
        // Удаляем is_active, так как его нет в схеме
    ];

    /**
     * Get the group name component.
     */
    public function nameComponent(): BelongsTo
    {
        return $this->belongsTo(GroupNameComponent::class, 'name_component_id');
    }

    /**
     * Получить куратора группы
     */
    public function curator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'curator_id');
    }

    /**
     * Получить студентов группы.
     */
    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'group_id');
    }

    /**
     * Получить преподавателей, связанных с группой.
     *
     * @return BelongsToMany
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subject_teacher_group', 'group_id', 'user_id')
            ->where('users.role', 'teacher')
            ->withTimestamps();
    }

    /**
     * Получить предметы, связанные с группой.
     *
     * @return BelongsToMany
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher_group', 'group_id', 'subject_id')
            ->withTimestamps();
    }

    /**
     * Получить задания для группы.
     *
     * @return HasMany
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Получить связи группы с преподавателями и предметами.
     *
     * @return HasMany
     */
    public function teacherSubjectConnections(): HasMany
    {
        return $this->hasMany(SubjectTeacherGroup::class);
    }
}
