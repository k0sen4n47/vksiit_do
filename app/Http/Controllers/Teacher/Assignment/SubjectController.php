<?php

namespace App\Http\Controllers\Teacher\Assignment;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    /**
     * Показать список предметов преподавателя
     */
    public function index()
    {
        try {
            $teacher = Auth::user();
            if (!$teacher) {
                Log::error('Пользователь не аутентифицирован');
                return redirect()->route('login');
            }

            // Получаем предметы преподавателя с группами
            $subjects = Subject::whereHas('teachers', function ($query) use ($teacher) {
                $query->where('users.id', $teacher->id);
            })->with(['groups' => function ($query) use ($teacher) {
                $query->whereHas('teachers', function ($q) use ($teacher) {
                    $q->where('users.id', $teacher->id);
                });
            }])->get();

            Log::info('Получен список предметов преподавателя', [
                'teacher_id' => $teacher->id,
                'subjects_count' => $subjects->count()
            ]);

            return view('teacher.assignment.subjects.index', compact('subjects'));

        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка предметов', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении списка предметов');
        }
    }
}
