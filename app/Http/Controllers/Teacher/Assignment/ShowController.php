<?php

namespace App\Http\Controllers\Teacher\Assignment;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Services\AssignmentService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShowController extends Controller
{
    protected AssignmentService $assignmentService;
    protected FileService $fileService;

    public function __construct(
        AssignmentService $assignmentService,
        FileService $fileService
    ) {
        $this->assignmentService = $assignmentService;
        $this->fileService = $fileService;
    }

    public function show(Assignment $assignment)
    {
        try {
            Log::info('Просмотр задания', [
                'assignment_id' => $assignment->id,
                'teacher_id' => Auth::id()
            ]);

            // Проверяем доступ преподавателя
            $teacher = Auth::user();
            if (!$teacher) {
                Log::error('Пользователь не аутентифицирован');
                return redirect()->route('login');
            }

            // Проверяем, является ли преподаватель автором задания
            if ($assignment->teacher_id !== $teacher->id) {
                Log::error('Отказано в доступе к просмотру задания', [
                    'teacher_id' => $teacher->id,
                    'assignment_id' => $assignment->id
                ]);
                return redirect()->back()->with('error', 'У вас нет доступа к этому заданию');
            }

            // Загружаем связанные данные
            $assignment->load(['subject', 'group', 'files', 'pages']);
            
            // Загружаем ответы студентов
            $studentAnswers = $assignment->answers()
                ->with(['student', 'assignmentPage'])
                ->orderBy('submitted_at', 'desc')
                ->get();

            return view('teacher.assignment.show', compact('assignment', 'studentAnswers'));

        } catch (\Exception $e) {
            Log::error('Ошибка при просмотре задания', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при просмотре задания');
        }
    }

    /**
     * Просмотр одного ответа студента на задание
     */
    public function studentAnswerShow(Assignment $assignment, $answerId)
    {
        $teacher = Auth::user();
        if (!$teacher) {
            return redirect()->route('login');
        }
        if ($assignment->teacher_id !== $teacher->id) {
            return redirect()->back()->with('error', 'У вас нет доступа к этому заданию');
        }
        $studentAnswer = $assignment->answers()->with(['student'])->findOrFail($answerId);
        return view('teacher.assignment.student-answer-show', compact('assignment', 'studentAnswer'));
    }
} 