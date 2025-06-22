<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Test;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\RoleMiddleware::class.':teacher');
    }

    /**
     * Показать список вопросов теста.
     */
    public function index(Test $test)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            $questions = $test->questions()
                ->with(['answers', 'studentAnswers'])
                ->orderBy('created_at', 'asc')
                ->get();

            return view('teacher.questions.index', compact('test', 'questions'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка вопросов', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении списка вопросов');
        }
    }

    /**
     * Показать форму создания вопроса.
     */
    public function create(Test $test)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            return view('teacher.questions.create', compact('test'));
        } catch (\Exception $e) {
            Log::error('Ошибка при создании вопроса', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при создании вопроса');
        }
    }

    /**
     * Сохранить новый вопрос.
     */
    public function store(Request $request, Test $test)
    {
        try {
            $teacher = Auth::user();
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            $validated = $request->validate([
                'question_text' => 'required|string',
                'type' => 'required|in:single,multiple,text',
                'points' => 'required|integer|min:1',
                'answers' => 'required_if:type,single,multiple|array',
                'answers.*.answer_text' => 'required_if:type,single,multiple|string',
                'answers.*.is_correct' => 'boolean'
            ]);

            DB::beginTransaction();

            // Создаем вопрос
            $question = Question::create([
                'test_id' => $test->id,
                'question_text' => $validated['question_text'],
                'type' => $validated['type'],
                'points' => $validated['points']
            ]);

            // Создаем ответы для закрытых вопросов
            if (in_array($validated['type'], ['single', 'multiple']) && isset($validated['answers'])) {
                foreach ($validated['answers'] as $answerData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $answerData['answer_text'],
                        'is_correct' => $answerData['is_correct'] ?? false
                    ]);
                }
            }

            DB::commit();

            Log::info('Вопрос создан успешно', [
                'question_id' => $question->id,
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.questions.index', $test)
                ->with('success', 'Вопрос успешно создан');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при создании вопроса', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при создании вопроса');
        }
    }

    /**
     * Показать вопрос с ответами.
     */
    public function show(Question $question)
    {
        try {
            $teacher = Auth::user();
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому вопросу');
            }

            $question->load(['answers', 'studentAnswers.student']);

            // Статистика по вопросу
            $statistics = $question->statistics;

            return view('teacher.questions.show', compact('question', 'test', 'statistics'));
        } catch (\Exception $e) {
            Log::error('Ошибка при просмотре вопроса', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при просмотре вопроса');
        }
    }

    /**
     * Показать форму редактирования вопроса.
     */
    public function edit(Question $question)
    {
        try {
            $teacher = Auth::user();
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому вопросу');
            }

            $question->load('answers');

            return view('teacher.questions.edit', compact('question', 'test'));
        } catch (\Exception $e) {
            Log::error('Ошибка при редактировании вопроса', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при редактировании вопроса');
        }
    }

    /**
     * Обновить вопрос.
     */
    public function update(Request $request, Question $question)
    {
        try {
            $teacher = Auth::user();
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому вопросу');
            }

            // Проверяем, есть ли ответы студентов на этот вопрос
            if ($question->studentAnswers()->count() > 0) {
                return redirect()->back()->with('error', 'Нельзя редактировать вопрос, на который уже есть ответы студентов');
            }

            $validated = $request->validate([
                'question_text' => 'required|string',
                'type' => 'required|in:single,multiple,text',
                'points' => 'required|integer|min:1',
                'answers' => 'required_if:type,single,multiple|array',
                'answers.*.answer_text' => 'required_if:type,single,multiple|string',
                'answers.*.is_correct' => 'boolean'
            ]);

            DB::beginTransaction();

            // Обновляем вопрос
            $question->update([
                'question_text' => $validated['question_text'],
                'type' => $validated['type'],
                'points' => $validated['points']
            ]);

            // Обновляем ответы для закрытых вопросов
            if (in_array($validated['type'], ['single', 'multiple']) && isset($validated['answers'])) {
                // Удаляем старые ответы
                $question->answers()->delete();
                
                // Создаем новые ответы
                foreach ($validated['answers'] as $answerData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $answerData['answer_text'],
                        'is_correct' => $answerData['is_correct'] ?? false
                    ]);
                }
            } else {
                // Для текстовых вопросов удаляем все ответы
                $question->answers()->delete();
            }

            DB::commit();

            Log::info('Вопрос обновлен успешно', [
                'question_id' => $question->id,
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.questions.show', $question)
                ->with('success', 'Вопрос успешно обновлен');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка при обновлении вопроса', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при обновлении вопроса');
        }
    }

    /**
     * Удалить вопрос.
     */
    public function destroy(Question $question)
    {
        try {
            $teacher = Auth::user();
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому вопросу');
            }

            // Проверяем, есть ли ответы студентов на этот вопрос
            if ($question->studentAnswers()->count() > 0) {
                return redirect()->back()->with('error', 'Нельзя удалить вопрос, на который уже есть ответы студентов');
            }

            $question->delete();

            Log::info('Вопрос удален успешно', [
                'question_id' => $question->id,
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.questions.index', $test)
                ->with('success', 'Вопрос успешно удален');

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении вопроса', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при удалении вопроса');
        }
    }

    /**
     * Показать статистику по вопросу.
     */
    public function statistics(Question $question)
    {
        try {
            $teacher = Auth::user();
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому вопросу');
            }

            $question->load(['studentAnswers.student', 'answers']);

            // Подробная статистика
            $statistics = [
                'basic' => $question->statistics,
                'answer_distribution' => $this->getAnswerDistribution($question),
                'student_answers' => $question->studentAnswers()->with('student')->paginate(20)
            ];

            return view('teacher.questions.statistics', compact('question', 'test', 'statistics'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении статистики вопроса', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении статистики');
        }
    }

    /**
     * Получить распределение ответов.
     */
    private function getAnswerDistribution(Question $question): array
    {
        if ($question->isTextQuestion()) {
            return [];
        }

        $distribution = [];
        $totalAnswers = $question->studentAnswers()->count();

        foreach ($question->answers as $answer) {
            $selectedCount = $question->studentAnswers()
                ->whereJsonContains('selected_answers', $answer->id)
                ->count();

            $distribution[] = [
                'answer' => $answer,
                'selected_count' => $selectedCount,
                'percentage' => $totalAnswers > 0 ? round(($selectedCount / $totalAnswers) * 100, 2) : 0
            ];
        }

        return $distribution;
    }
}
