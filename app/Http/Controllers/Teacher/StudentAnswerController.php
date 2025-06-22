<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\StudentAnswer;
use App\Models\Question;
use App\Models\TestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentAnswerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\RoleMiddleware::class.':teacher');
    }

    /**
     * Показать список ответов студентов на вопрос.
     */
    public function index(Question $question)
    {
        try {
            $teacher = Auth::user();
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому вопросу');
            }

            $studentAnswers = $question->studentAnswers()
                ->with(['student', 'testResult'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('teacher.student-answers.index', compact('question', 'test', 'studentAnswers'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка ответов студентов', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении списка ответов');
        }
    }

    /**
     * Показать ответ студента.
     */
    public function show(StudentAnswer $studentAnswer)
    {
        try {
            $teacher = Auth::user();
            $question = $studentAnswer->question;
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому ответу');
            }

            $studentAnswer->load(['student', 'testResult', 'question.answers']);

            return view('teacher.student-answers.show', compact('studentAnswer', 'question', 'test'));
        } catch (\Exception $e) {
            Log::error('Ошибка при просмотре ответа студента', [
                'error' => $e->getMessage(),
                'student_answer_id' => $studentAnswer->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при просмотре ответа');
        }
    }

    /**
     * Показать форму редактирования ответа студента.
     */
    public function edit(StudentAnswer $studentAnswer)
    {
        try {
            $teacher = Auth::user();
            $question = $studentAnswer->question;
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому ответу');
            }

            $studentAnswer->load(['student', 'testResult', 'question.answers']);

            return view('teacher.student-answers.edit', compact('studentAnswer', 'question', 'test'));
        } catch (\Exception $e) {
            Log::error('Ошибка при редактировании ответа студента', [
                'error' => $e->getMessage(),
                'student_answer_id' => $studentAnswer->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при редактировании ответа');
        }
    }

    /**
     * Обновить ответ студента (оценка и комментарий).
     */
    public function update(Request $request, StudentAnswer $studentAnswer)
    {
        try {
            $teacher = Auth::user();
            $question = $studentAnswer->question;
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому ответу');
            }

            $validated = $request->validate([
                'points_earned' => 'required|integer|min:0|max:' . $question->points,
                'teacher_comment' => 'nullable|string|max:1000',
                'is_correct' => 'boolean'
            ]);

            $studentAnswer->update($validated);

            // Пересчитываем общий балл за тест
            $this->recalculateTestScore($studentAnswer->testResult);

            Log::info('Ответ студента обновлен', [
                'student_answer_id' => $studentAnswer->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.student-answers.show', $studentAnswer)
                ->with('success', 'Ответ успешно обновлен');

        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении ответа студента', [
                'error' => $e->getMessage(),
                'student_answer_id' => $studentAnswer->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при обновлении ответа');
        }
    }

    /**
     * Удалить ответ студента.
     */
    public function destroy(StudentAnswer $studentAnswer)
    {
        try {
            $teacher = Auth::user();
            $question = $studentAnswer->question;
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому ответу');
            }

            $studentAnswer->delete();

            // Пересчитываем общий балл за тест
            $this->recalculateTestScore($studentAnswer->testResult);

            Log::info('Ответ студента удален', [
                'student_answer_id' => $studentAnswer->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.student-answers.index', $question)
                ->with('success', 'Ответ успешно удален');

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении ответа студента', [
                'error' => $e->getMessage(),
                'student_answer_id' => $studentAnswer->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при удалении ответа');
        }
    }

    /**
     * Показать ответы студента на все вопросы теста.
     */
    public function studentTestAnswers(TestResult $testResult)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к результату
            if ($testResult->test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому результату');
            }

            $studentAnswers = $testResult->studentAnswers()
                ->with(['question.answers'])
                ->orderBy('created_at', 'asc')
                ->get();

            return view('teacher.student-answers.student-test-answers', compact('testResult', 'studentAnswers'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении ответов студента на тест', [
                'error' => $e->getMessage(),
                'test_result_id' => $testResult->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении ответов');
        }
    }

    /**
     * Массовая проверка ответов на вопрос.
     */
    public function bulkReview(Question $question)
    {
        try {
            $teacher = Auth::user();
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому вопросу');
            }

            $studentAnswers = $question->studentAnswers()
                ->with(['student', 'testResult'])
                ->orderBy('created_at', 'asc')
                ->get();

            return view('teacher.student-answers.bulk-review', compact('question', 'test', 'studentAnswers'));
        } catch (\Exception $e) {
            Log::error('Ошибка при массовой проверке ответов', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при массовой проверке');
        }
    }

    /**
     * Сохранить массовую проверку ответов.
     */
    public function bulkReviewStore(Request $request, Question $question)
    {
        try {
            $teacher = Auth::user();
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому вопросу');
            }

            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*.id' => 'required|exists:student_answers,id',
                'answers.*.points_earned' => 'required|integer|min:0|max:' . $question->points,
                'answers.*.teacher_comment' => 'nullable|string|max:1000',
                'answers.*.is_correct' => 'boolean'
            ]);

            foreach ($validated['answers'] as $answerData) {
                $studentAnswer = StudentAnswer::find($answerData['id']);
                if ($studentAnswer && $studentAnswer->question_id === $question->id) {
                    $studentAnswer->update([
                        'points_earned' => $answerData['points_earned'],
                        'teacher_comment' => $answerData['teacher_comment'] ?? null,
                        'is_correct' => $answerData['is_correct'] ?? false
                    ]);

                    // Пересчитываем общий балл за тест
                    $this->recalculateTestScore($studentAnswer->testResult);
                }
            }

            Log::info('Массовая проверка ответов завершена', [
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.student-answers.index', $question)
                ->with('success', 'Массовая проверка завершена успешно');

        } catch (\Exception $e) {
            Log::error('Ошибка при массовой проверке ответов', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при массовой проверке');
        }
    }

    /**
     * Пересчитать общий балл за тест.
     */
    private function recalculateTestScore(TestResult $testResult): void
    {
        $totalScore = $testResult->studentAnswers()->sum('points_earned');
        $testResult->update(['score' => $totalScore]);
    }
}
