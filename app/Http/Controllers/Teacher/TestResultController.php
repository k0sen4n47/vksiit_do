<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TestResult;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TestResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\RoleMiddleware::class.':teacher');
    }

    /**
     * Показать список результатов тестирования.
     */
    public function index()
    {
        try {
            $teacher = Auth::user();
            
            // Получаем результаты тестов преподавателя
            $results = TestResult::whereHas('test.assignmentPage.assignment', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->with(['test.assignmentPage.assignment.subject', 'test.assignmentPage.assignment.group', 'student'])
              ->orderBy('created_at', 'desc')
              ->paginate(20);

            return view('teacher.test-results.index', compact('results'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка результатов', [
                'error' => $e->getMessage(),
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении списка результатов');
        }
    }

    /**
     * Показать результат тестирования конкретного студента.
     */
    public function show(TestResult $testResult)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к результату
            if ($testResult->test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому результату');
            }

            $testResult->load([
                'test.assignmentPage.assignment.subject',
                'test.assignmentPage.assignment.group',
                'student',
                'studentAnswers.question.answers'
            ]);

            return view('teacher.test-results.show', compact('testResult'));
        } catch (\Exception $e) {
            Log::error('Ошибка при просмотре результата', [
                'error' => $e->getMessage(),
                'test_result_id' => $testResult->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при просмотре результата');
        }
    }

    /**
     * Показать форму редактирования результата (комментарий преподавателя).
     */
    public function edit(TestResult $testResult)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к результату
            if ($testResult->test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому результату');
            }

            $testResult->load([
                'test.assignmentPage.assignment.subject',
                'test.assignmentPage.assignment.group',
                'student'
            ]);

            return view('teacher.test-results.edit', compact('testResult'));
        } catch (\Exception $e) {
            Log::error('Ошибка при редактировании результата', [
                'error' => $e->getMessage(),
                'test_result_id' => $testResult->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при редактировании результата');
        }
    }

    /**
     * Обновить результат (комментарий преподавателя).
     */
    public function update(Request $request, TestResult $testResult)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к результату
            if ($testResult->test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому результату');
            }

            $validated = $request->validate([
                'teacher_comment' => 'nullable|string|max:1000',
                'score' => 'nullable|integer|min:0|max:' . $testResult->max_score
            ]);

            $testResult->update($validated);

            Log::info('Результат тестирования обновлен', [
                'test_result_id' => $testResult->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.test-results.show', $testResult)
                ->with('success', 'Результат успешно обновлен');

        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении результата', [
                'error' => $e->getMessage(),
                'test_result_id' => $testResult->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при обновлении результата');
        }
    }

    /**
     * Удалить результат тестирования.
     */
    public function destroy(TestResult $testResult)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к результату
            if ($testResult->test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому результату');
            }

            $testResult->delete();

            Log::info('Результат тестирования удален', [
                'test_result_id' => $testResult->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.test-results.index')
                ->with('success', 'Результат успешно удален');

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении результата', [
                'error' => $e->getMessage(),
                'test_result_id' => $testResult->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при удалении результата');
        }
    }

    /**
     * Показать результаты конкретного теста.
     */
    public function testResults(Test $test)
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

            return view('teacher.test-results.test-results', compact('test', 'results'));
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
     * Показать результаты конкретного студента.
     */
    public function studentResults(User $student)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем, что студент из группы преподавателя
            if (!$student->group || !$teacher->groups->contains($student->group)) {
                return redirect()->back()->with('error', 'У вас нет доступа к результатам этого студента');
            }

            $results = TestResult::where('student_id', $student->id)
                ->whereHas('test.assignmentPage.assignment', function ($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                })->with(['test.assignmentPage.assignment.subject', 'test.assignmentPage.assignment.group'])
                  ->orderBy('created_at', 'desc')
                  ->paginate(20);

            return view('teacher.test-results.student-results', compact('student', 'results'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении результатов студента', [
                'error' => $e->getMessage(),
                'student_id' => $student->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении результатов студента');
        }
    }

    /**
     * Экспорт результатов теста.
     */
    public function export(Test $test)
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
                ->get();

            // Здесь будет логика экспорта в Excel/CSV
            // Пока возвращаем JSON для API
            return response()->json([
                'test' => $test->load('assignmentPage.assignment.subject'),
                'results' => $results,
                'statistics' => [
                    'total_results' => $results->count(),
                    'completed_results' => $results->where('status', 'completed')->count(),
                    'average_score' => $results->where('status', 'completed')->avg('score') ?? 0,
                    'passing_rate' => $this->calculatePassingRate($test, $results)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при экспорте результатов', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при экспорте результатов');
        }
    }

    /**
     * Рассчитать процент прохождения теста.
     */
    private function calculatePassingRate(Test $test, $results): float
    {
        $completedResults = $results->where('status', 'completed');
        $totalCompleted = $completedResults->count();
        
        if ($totalCompleted === 0) {
            return 0;
        }

        $passedCount = $completedResults->where('score', '>=', $test->passing_score)->count();
        return round(($passedCount / $totalCompleted) * 100, 2);
    }
}
