<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_id',
        'group_id',
        'teacher_id',
        'title',
        'description',
        'deadline',
        'max_score',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deadline' => 'datetime',
        'max_score' => 'integer',
    ];

    // Define relationships here if needed (e.g., to Subject, Group, User)

    /**
     * Get the subject that owns the assignment.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher that owns the assignment.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the files for the assignment.
     */
    public function files(): HasMany
    {
        return $this->hasMany(AssignmentFile::class);
    }

    /**
     * Get the groups that are assigned to this assignment.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class)
            ->withTimestamps();
    }

    /**
     * Get the primary group for this assignment.
     */
    public function primaryGroup(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Get the group for this assignment (alias for primaryGroup).
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Получить страницы задания.
     *
     * @return HasMany
     */
    public function pages(): HasMany
    {
        return $this->hasMany(AssignmentPage::class);
    }

    /**
     * Получить страницы задания (alias для pages).
     */
    public function assignmentPages(): HasMany
    {
        return $this->hasMany(AssignmentPage::class);
    }

    /**
     * Получить ответы студентов на это задание.
     */
    public function answers()
    {
        return $this->hasManyThrough(
            \App\Models\AssignmentStudentAnswer::class,
            \App\Models\AssignmentPage::class,
            'assignment_id', // Foreign key on AssignmentPage
            'assignment_page_id', // Foreign key on AssignmentStudentAnswer
            'id', // Local key on Assignment
            'id'  // Local key on AssignmentPage
        );
    }

    /**
     * Проверить, является ли задание активным.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Проверить, является ли задание выполненным.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Проверить, является ли задание архивным.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Отметить задание как выполненное.
     */
    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    /**
     * Отметить задание как активное.
     */
    public function markAsActive(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Отметить задание как архивное.
     */
    public function markAsArchived(): void
    {
        $this->update(['status' => 'archived']);
    }
} 