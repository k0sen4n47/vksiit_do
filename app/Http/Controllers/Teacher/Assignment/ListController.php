<?php

namespace App\Http\Controllers\Teacher\Assignment;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ListController extends Controller
{
    /**
     * Показать список заданий преподавателя
     */
    public function index()
    {
        try {
            $teacher = Auth::user();
            if (!$teacher) {
                Log::error('Пользователь не аутентифицирован');
                return redirect()->route('login');
            }

            // Получаем задания преподавателя с предзагрузкой связей
            $assignments = Assignment::where('teacher_id', $teacher->id)
                ->with(['subject', 'primaryGroup', 'pages'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Получаем предметы преподавателя с группами для быстрого создания
            $teacherSubjects = Subject::whereHas('teachers', function ($query) use ($teacher) {
                $query->where('users.id', $teacher->id);
            })->with(['groups' => function ($query) use ($teacher) {
                $query->whereHas('teachers', function ($q) use ($teacher) {
                    $q->where('users.id', $teacher->id);
                });
            }])->get();

            Log::info('Получен список заданий преподавателя', [
                'teacher_id' => $teacher->id,
                'assignments_count' => $assignments->count(),
                'subjects_count' => $teacherSubjects->count()
            ]);

            return view('teacher.assignment.list', compact('assignments', 'teacherSubjects'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка заданий', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении списка заданий');
        }
    }
} 