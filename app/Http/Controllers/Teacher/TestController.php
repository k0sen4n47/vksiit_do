<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\AssignmentPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\RoleMiddleware::class.':teacher');
    }

    /**
     * Показать список всех тестов преподавателя.
     */
    public function index()
    {
        try {
            $teacher = Auth::user();
            
            // Получаем тесты, связанные с заданиями преподавателя
            $tests = Test::whereHas('assignmentPage.assignment', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->with(['assignmentPage.assignment.subject', 'assignmentPage.assignment.group'])
              ->withCount(['questions', 'testResults'])
              ->orderBy('created_at', 'desc')
              ->get();

            return view('teacher.tests.index', compact('tests'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка тестов', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении списка тестов');
        }
    }

    /**
     * Показать форму создания теста.
     */
    public function create()
    {
        $teacher = Auth::user();
        $assignmentPages = AssignmentPage::whereHas('assignment', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->where('type', 'test')
          ->whereNull('test_id')
          ->with(['assignment.subject', 'assignment.group'])
          ->get();

        return view('teacher.tests.create', compact('assignmentPages'));
    }

    /**
     * Сохранить новый тест.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'time_limit' => 'nullable|integer|min:1',
                'passing_score' => 'nullable|integer|min:0',
                'assignment_page_id' => 'required|exists:assignment_pages,id',
                'is_active' => 'boolean'
            ]);

            DB::beginTransaction();

            // Создаем тест
            $test = Test::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'time_limit' => $validated['time_limit'],
                'passing_score' => $validated['passing_score'],
                'is_active' => $validated['is_active'] ?? true
            ]);

            // Связываем тест со страницей задания
            $assignmentPage = AssignmentPage::findOrFail($validated['assignment_page_id']);
            $assignmentPage->update(['test_id' => $test->id]);

            DB::commit();

            Log::info('Тест создан успешно', [
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.tests.show', $test)
                ->with('success', 'Тест успешно создан');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при создании теста', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при создании теста');
        }
    }

    /**
     * Показать тест с вопросами.
     */
    public function show(Test $test)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            $test->load(['questions.answers', 'assignmentPage.assignment.subject', 'assignmentPage.assignment.group']);
            
            // Статистика по тесту
            $statistics = [
                'total_results' => $test->testResults()->count(),
                'completed_results' => $test->testResults()->where('status', 'completed')->count(),
                'average_score' => $test->testResults()->where('status', 'completed')->avg('score') ?? 0,
                'max_score' => $test->max_score
            ];

            return view('teacher.tests.show', compact('test', 'statistics'));
        } catch (\Exception $e) {
            Log::error('Ошибка при просмотре теста', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при просмотре теста');
        }
    }

    /**
     * Показать форму редактирования теста.
     */
    public function edit(Test $test)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            $test->load(['assignmentPage.assignment.subject', 'assignmentPage.assignment.group']);

            return view('teacher.tests.edit', compact('test'));
        } catch (\Exception $e) {
            Log::error('Ошибка при редактировании теста', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при редактировании теста');
        }
    }

    /**
     * Обновить тест.
     */
    public function update(Request $request, Test $test)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            // Проверяем, есть ли результаты тестирования
            if ($test->testResults()->count() > 0) {
                return redirect()->back()->with('error', 'Нельзя редактировать тест, на который уже есть результаты');
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'time_limit' => 'nullable|integer|min:1',
                'passing_score' => 'required|integer|min:1',
                'max_attempts' => 'nullable|integer|min:1',
                'shuffle_questions' => 'boolean',
                'show_results' => 'boolean'
            ]);

            $test->update($validated);

            Log::info('Тест обновлен успешно', [
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.tests.show', $test)
                ->with('success', 'Тест успешно обновлен');

        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении теста', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при обновлении теста');
        }
    }

    /**
     * Удалить тест.
     */
    public function destroy(Test $test)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            // Проверяем, есть ли результаты тестирования
            if ($test->testResults()->count() > 0) {
                return redirect()->back()->with('error', 'Нельзя удалить тест, на который уже есть результаты');
            }

            DB::beginTransaction();

            // Отвязываем тест от страницы задания
            $test->assignmentPage->update(['test_id' => null]);
            
            // Удаляем тест (вопросы и ответы удалятся каскадно)
            $test->delete();

            DB::commit();

            Log::info('Тест удален успешно', [
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.tests.index')
                ->with('success', 'Тест успешно удален');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при удалении теста', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при удалении теста');
        }
    }

    /**
     * Показать результаты тестирования.
     */
    public function results(Test $test)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            $results = $test->testResults()
                ->with(['student', 'studentAnswers.question'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('teacher.tests.results', compact('test', 'results'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении результатов теста', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении результатов');
        }
    }

    /**
     * Показать статистику по тесту.
     */
    public function statistics(Test $test)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            $test->load(['questions.studentAnswers', 'testResults.student']);

            // Подробная статистика
            $statistics = [
                'total_results' => $test->testResults()->count(),
                'completed_results' => $test->testResults()->where('status', 'completed')->count(),
                'in_progress_results' => $test->testResults()->where('status', 'in_progress')->count(),
                'timeout_results' => $test->testResults()->where('status', 'timeout')->count(),
                'average_score' => $test->testResults()->where('status', 'completed')->avg('score') ?? 0,
                'max_score' => $test->max_score,
                'passing_rate' => $this->calculatePassingRate($test),
                'question_statistics' => $this->getQuestionStatistics($test)
            ];

            return view('teacher.tests.statistics', compact('test', 'statistics'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении статистики теста', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении статистики');
        }
    }

    /**
     * Рассчитать процент прохождения теста.
     */
    private function calculatePassingRate(Test $test): float
    {
        $completedResults = $test->testResults()->where('status', 'completed');
        $totalCompleted = $completedResults->count();
        
        if ($totalCompleted === 0) {
            return 0;
        }

        $passedCount = $completedResults->where('score', '>=', $test->passing_score)->count();
        return round(($passedCount / $totalCompleted) * 100, 2);
    }

    /**
     * Получить статистику по вопросам.
     */
    private function getQuestionStatistics(Test $test): array
    {
        $statistics = [];
        
        foreach ($test->questions as $question) {
            $statistics[] = [
                'question' => $question,
                'statistics' => $question->statistics
            ];
        }

        return $statistics;
    }
}
