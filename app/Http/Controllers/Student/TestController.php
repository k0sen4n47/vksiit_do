<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\RoleMiddleware::class.':student');
    }

    /**
     * Показать тест для прохождения.
     */
    public function take(TestResult $testResult)
    {
        try {
            $student = Auth::user();
            
            // Проверяем доступ к результату
            if ($testResult->student_id !== $student->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            // Проверяем, что тест еще не завершен
            if ($testResult->status === 'completed') {
                return redirect()->route('student.tests.result', $testResult)
                    ->with('info', 'Вы уже завершили этот тест');
            }

            // Проверяем, не истекло ли время
            if ($testResult->test->time_limit) {
                $elapsedTime = $testResult->started_at->diffInMinutes(now());
                if ($elapsedTime >= $testResult->test->time_limit) {
                    $testResult->update([
                        'status' => 'timeout',
                        'completed_at' => now()
                    ]);
                    return redirect()->route('student.tests.result', $testResult)
                        ->with('error', 'Время прохождения теста истекло');
                }
            }

            $testResult->load(['test.questions.answers']);

            // Перемешиваем вопросы, если нужно
            $questions = $testResult->test->questions;
            if ($testResult->test->shuffle_questions) {
                $questions = $questions->shuffle();
            }

            return view('student.tests.take', compact('testResult', 'questions'));

        } catch (\Exception $e) {
            Log::error('Ошибка при прохождении теста', [
                'error' => $e->getMessage(),
                'test_result_id' => $testResult->id,
                'student_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при загрузке теста');
        }
    }

    /**
     * Сохранить ответы студента.
     */
    public function saveAnswers(Request $request, TestResult $testResult)
    {
        try {
            $student = Auth::user();
            
            // Проверяем доступ к результату
            if ($testResult->student_id !== $student->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому тесту'
                ], 403);
            }

            // Проверяем, что тест еще не завершен
            if ($testResult->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Тест уже завершен'
                ], 400);
            }

            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|exists:questions,id',
                'answers.*.answer_text' => 'nullable|string',
                'answers.*.selected_answers' => 'nullable|array',
                'answers.*.selected_answers.*' => 'integer|exists:answers,id'
            ]);

            DB::beginTransaction();

            foreach ($validated['answers'] as $answerData) {
                // Проверяем, что вопрос принадлежит этому тесту
                $question = $testResult->test->questions()->find($answerData['question_id']);
                if (!$question) {
                    continue;
                }

                // Создаем или обновляем ответ студента
                StudentAnswer::updateOrCreate(
                    [
                        'test_result_id' => $testResult->id,
                        'question_id' => $answerData['question_id']
                    ],
                    [
                        'answer_text' => $answerData['answer_text'] ?? null,
                        'selected_answers' => $answerData['selected_answers'] ?? null,
                        'points_earned' => 0, // Будет рассчитано при завершении
                        'is_correct' => false // Будет рассчитано при завершении
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ответы сохранены'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при сохранении ответов', [
                'error' => $e->getMessage(),
                'test_result_id' => $testResult->id,
                'student_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при сохранении ответов'
            ], 500);
        }
    }

    /**
     * Завершить тест.
     */
    public function finish(Request $request, TestResult $testResult)
    {
        try {
            $student = Auth::user();
            
            // Проверяем доступ к результату
            if ($testResult->student_id !== $student->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому тесту'
                ], 403);
            }

            // Проверяем, что тест еще не завершен
            if ($testResult->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Тест уже завершен'
                ], 400);
            }

            DB::beginTransaction();

            // Сохраняем финальные ответы
            if ($request->has('answers')) {
                $validated = $request->validate([
                    'answers' => 'required|array',
                    'answers.*.question_id' => 'required|exists:questions,id',
                    'answers.*.answer_text' => 'nullable|string',
                    'answers.*.selected_answers' => 'nullable|array',
                    'answers.*.selected_answers.*' => 'integer|exists:answers,id'
                ]);

                foreach ($validated['answers'] as $answerData) {
                    $question = $testResult->test->questions()->find($answerData['question_id']);
                    if (!$question) {
                        continue;
                    }

                    StudentAnswer::updateOrCreate(
                        [
                            'test_result_id' => $testResult->id,
                            'question_id' => $answerData['question_id']
                        ],
                        [
                            'answer_text' => $answerData['answer_text'] ?? null,
                            'selected_answers' => $answerData['selected_answers'] ?? null,
                            'points_earned' => 0,
                            'is_correct' => false
                        ]
                    );
                }
            }

            // Рассчитываем результаты
            $this->calculateTestResults($testResult);

            // Завершаем тест
            $testResult->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            DB::commit();

            Log::info('Студент завершил тест', [
                'test_result_id' => $testResult->id,
                'test_id' => $testResult->test_id,
                'student_id' => $student->id,
                'score' => $testResult->score
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Тест завершен',
                'redirect_url' => route('student.tests.result', $testResult)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при завершении теста', [
                'error' => $e->getMessage(),
                'test_result_id' => $testResult->id,
                'student_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при завершении теста'
            ], 500);
        }
    }

    /**
     * Показать результат тестирования.
     */
    public function result(TestResult $testResult)
    {
        try {
            $student = Auth::user();
            
            // Проверяем доступ к результату
            if ($testResult->student_id !== $student->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому результату');
            }

            $testResult->load([
                'test.assignmentPage.assignment.subject',
                'test.assignmentPage.assignment.teacher',
                'studentAnswers.question.answers'
            ]);

            return view('student.tests.result', compact('testResult'));

        } catch (\Exception $e) {
            Log::error('Ошибка при просмотре результата теста', [
                'error' => $e->getMessage(),
                'test_result_id' => $testResult->id,
                'student_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при просмотре результата');
        }
    }

    /**
     * Рассчитать результаты теста.
     */
    private function calculateTestResults(TestResult $testResult): void
    {
        $totalScore = 0;
        
        foreach ($testResult->studentAnswers as $studentAnswer) {
            $question = $studentAnswer->question;
            $pointsEarned = 0;
            $isCorrect = false;

            if ($question->type === 'text') {
                // Для текстовых вопросов пока не оцениваем автоматически
                $pointsEarned = 0;
                $isCorrect = false;
            } else {
                // Для закрытых вопросов проверяем правильность
                $correctAnswers = $question->correctAnswers->pluck('id')->toArray();
                $selectedAnswers = $studentAnswer->selected_answers ?? [];

                if ($question->type === 'single') {
                    // Для одиночного выбора
                    if (count($selectedAnswers) === 1 && in_array($selectedAnswers[0], $correctAnswers)) {
                        $pointsEarned = $question->points;
                        $isCorrect = true;
                    }
                } else {
                    // Для множественного выбора
                    if (count($selectedAnswers) === count($correctAnswers) && 
                        empty(array_diff($selectedAnswers, $correctAnswers))) {
                        $pointsEarned = $question->points;
                        $isCorrect = true;
                    }
                }
            }

            // Обновляем ответ студента
            $studentAnswer->update([
                'points_earned' => $pointsEarned,
                'is_correct' => $isCorrect
            ]);

            $totalScore += $pointsEarned;
        }

        // Обновляем общий результат
        $testResult->update(['score' => $totalScore]);
    }
} 