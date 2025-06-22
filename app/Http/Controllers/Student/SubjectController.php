<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subject;
use App\Models\User;
use App\Models\Group;
use App\Models\SubjectTeacherGroup;
use App\Models\Assignment;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();
        // Получаем группу студента
        $groupId = $student->group_id;
        // Получаем id предметов, связанных с группой студента
        $subjectIds = SubjectTeacherGroup::where('group_id', $groupId)->pluck('subject_id');
        // Получаем сами предметы
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return view('student.subjects', compact('subjects'));
    }

    public function show($subjectId)
    {
        $student = Auth::user();
        $groupId = $student->group_id;
        // Проверяем, что предмет доступен студенту
        $isAvailable = \App\Models\SubjectTeacherGroup::where('group_id', $groupId)
            ->where('subject_id', $subjectId)
            ->exists();
        if (!$isAvailable) {
            abort(403, 'Доступ запрещён');
        }
        $subject = \App\Models\Subject::findOrFail($subjectId);
        
        // Получаем фильтр из запроса
        $filter = request('filter', 'active');
        
        // Получаем все задания по предмету для группы студента с фильтрацией
        $assignmentsQuery = \App\Models\Assignment::where('subject_id', $subjectId)
            ->where('group_id', $groupId);
            
        // Применяем фильтр
        if ($filter === 'active') {
            $assignmentsQuery->where('status', 'active');
        } elseif ($filter === 'completed') {
            $assignmentsQuery->where('status', 'completed');
        } elseif ($filter === 'archived') {
            $assignmentsQuery->where('status', 'archived');
        }
        
        $assignments = $assignmentsQuery->orderBy('deadline', 'asc')->get();
        
        return view('student.subject_show', compact('subject', 'assignments', 'filter'));
    }
} 