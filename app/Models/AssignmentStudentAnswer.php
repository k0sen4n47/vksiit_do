<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignmentStudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_page_id',
        'student_id',
        'answer_text',
        'answer_html',
        'answer_css',
        'files',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'files' => 'array',
    ];

    /**
     * Получить страницу задания.
     */
    public function assignmentPage()
    {
        return $this->belongsTo(AssignmentPage::class);
    }

    /**
     * Получить студента.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Получить задание через страницу.
     */
    public function assignment()
    {
        return $this->hasOneThrough(
            Assignment::class,
            AssignmentPage::class,
            'id',
            'id',
            'assignment_page_id',
            'assignment_id'
        );
    }
} 