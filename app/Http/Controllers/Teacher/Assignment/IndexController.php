<?php

namespace App\Http\Controllers\Teacher\Assignment;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Group;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    /**
     * Показать список заданий
     */
    public function index(Request $request, $subjectId, $groupId)
    {
        try {
            Log::info('Просмотр списка заданий', [
                'teacher_id' => Auth::id(),
                'subject_id' => $subjectId,
                'group_id' => $groupId
            ]);

            // Получаем предмет и группу
            $subject = Subject::findOrFail($subjectId);
            $group = Group::findOrFail($groupId);

            // Проверяем доступ преподавателя
            $teacher = Auth::user();
            if (!$teacher) {
                Log::error('Пользователь не аутентифицирован');
                return redirect()->route('login');
            }

            $hasAccess = $subject->teachers()
                ->where('users.id', $teacher->id)
                ->whereHas('groups', function ($query) use ($group) {
                    $query->where('groups.id', $group->id);
                })
                ->exists();

            if (!$hasAccess) {
                Log::error('Отказано в доступе к списку заданий', [
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'group_id' => $group->id
                ]);
                return redirect()->back()->with('error', 'У вас нет доступа к заданиям для этой группы');
            }

            // Получаем задания
            $assignments = Assignment::where('subject_id', $subjectId)
                ->where('group_id', $groupId)
                ->where('teacher_id', $teacher->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('teacher.assignment.index', compact('assignments', 'subject', 'group'));

        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка заданий', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении списка заданий');
        }
    }
} 