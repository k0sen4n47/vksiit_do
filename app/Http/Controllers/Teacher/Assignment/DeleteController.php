<?php

namespace App\Http\Controllers\Teacher\Assignment;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Services\AssignmentService;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeleteController extends Controller
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

    public function destroy(Assignment $assignment)
    {
        try {
            Log::info('Удаление задания', [
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
                Log::error('Отказано в доступе к удалению задания', [
                    'teacher_id' => $teacher->id,
                    'assignment_id' => $assignment->id
                ]);
                return redirect()->back()->with('error', 'У вас нет доступа к удалению этого задания');
            }

            // Удаляем все файлы задания
            $files = $this->fileService->getAssignmentFiles($assignment->id);
            foreach ($files as $file) {
                $this->fileService->deleteFile($file->id);
            }

            // Удаляем задание
            $this->assignmentService->deleteAssignment($assignment);

            Log::info('Задание успешно удалено', [
                'assignment_id' => $assignment->id,
                'teacher_id' => $teacher->id
            ]);

            return redirect()
                ->route('teacher.assignments.index')
                ->with('success', 'Задание успешно удалено');

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении задания', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при удалении задания');
        }
    }
} 