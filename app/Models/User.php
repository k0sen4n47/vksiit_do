<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'login',
        'password',
        'role',
        'group_id',
        'fio',
        'subgroup',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the subjects the user (teacher) is associated with through the pivot table.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher_group', 'user_id', 'subject_id');
    }

    /**
     * Get the groups the user (teacher) is associated with through the pivot table.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'subject_teacher_group', 'user_id', 'group_id');
    }

    /**
     * Get the subjects and groups the user (teacher) is associated with.
     */
    public function subjectsAndGroups(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher_group', 'user_id', 'subject_id')
                    ->withPivot('group_id'); // Включаем поле group_id из промежуточной таблицы
    }

    /**
     * Получить группу, к которой относится пользователь (если он студент).
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Получить группу, куратором которой является пользователь (если он преподаватель).
     */
    public function isCurator(): HasOne
    {
        return $this->hasOne(Group::class, 'curator_id');
    }

    /**
     * Проверяет, является ли пользователь преподавателем.
     *
     * @return bool
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Проверяет, является ли пользователь студентом.
     *
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Проверяет, является ли пользователь администратором.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Получить URL фото пользователя (или фото по умолчанию).
     */
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo && file_exists(public_path('images/' . $this->photo))) {
            return asset('images/' . $this->photo);
        }
        if ($this->role === 'teacher') {
            return asset('images/default-teacher.png');
        }
        return asset('images/default-student.png');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }
}
