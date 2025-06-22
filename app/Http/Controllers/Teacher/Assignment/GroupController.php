<?php

namespace App\Http\Controllers\Teacher\Assignment;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    /**
     * Показать список групп для выбранного предмета
     */
    public function index(Request $request, $subjectId)
    {
        try {
            $teacher = Auth::user();
            if (!$teacher) {
                Log::error('Пользователь не аутентифицирован');
                return redirect()->route('login');
            }

            // Получаем предмет
            $subject = Subject::findOrFail($subjectId);

            // Проверяем доступ преподавателя к предмету
            if (!$subject->teachers()->where('users.id', $teacher->id)->exists()) {
                Log::error('Отказано в доступе к предмету', [
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id
                ]);
                return redirect()->back()->with('error', 'У вас нет доступа к этому предмету');
            }

            // Получаем группы, связанные с предметом и преподавателем
            $groups = Group::whereHas('subjects', function ($query) use ($subject) {
                $query->where('subjects.id', $subject->id);
            })->whereHas('teachers', function ($query) use ($teacher) {
                $query->where('users.id', $teacher->id);
            })->get();

            Log::info('Получен список групп для предмета', [
                'teacher_id' => $teacher->id,
                'subject_id' => $subject->id,
                'groups_count' => $groups->count()
            ]);

            return view('teacher.assignment.groups.index', compact('subject', 'groups'));

        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка групп', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении списка групп');
        }
    }
} 