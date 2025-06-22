<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AnswerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\RoleMiddleware::class.':teacher');
    }

    /**
     * Показать список ответов на вопрос.
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

            $answers = $question->answers()->orderBy('created_at', 'asc')->get();

            return view('teacher.answers.index', compact('question', 'test', 'answers'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка ответов', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении списка ответов');
        }
    }

    /**
     * Показать форму создания ответа.
     */
    public function create(Question $question)
    {
        try {
            $teacher = Auth::user();
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому вопросу');
            }

            // Проверяем, что вопрос не текстовый
            if ($question->isTextQuestion()) {
                return redirect()->back()->with('error', 'Для текстовых вопросов не нужны варианты ответов');
            }

            return view('teacher.answers.create', compact('question', 'test'));
        } catch (\Exception $e) {
            Log::error('Ошибка при создании ответа', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при создании ответа');
        }
    }

    /**
     * Сохранить новый ответ.
     */
    public function store(Request $request, Question $question)
    {
        try {
            $teacher = Auth::user();
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому вопросу');
            }

            // Проверяем, что вопрос не текстовый
            if ($question->isTextQuestion()) {
                return redirect()->back()->with('error', 'Для текстовых вопросов не нужны варианты ответов');
            }

            $validated = $request->validate([
                'answer_text' => 'required|string|max:1000',
                'is_correct' => 'boolean'
            ]);

            // Проверяем, есть ли ответы студентов на этот вопрос
            if ($question->studentAnswers()->count() > 0) {
                return redirect()->back()->with('error', 'Нельзя добавлять ответы к вопросу, на который уже есть ответы студентов');
            }

            Answer::create([
                'question_id' => $question->id,
                'answer_text' => $validated['answer_text'],
                'is_correct' => $validated['is_correct'] ?? false
            ]);

            Log::info('Ответ создан успешно', [
                'question_id' => $question->id,
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.answers.index', $question)
                ->with('success', 'Ответ успешно создан');

        } catch (\Exception $e) {
            Log::error('Ошибка при создании ответа', [
                'error' => $e->getMessage(),
                'question_id' => $question->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при создании ответа');
        }
    }

    /**
     * Показать ответ.
     */
    public function show(Answer $answer)
    {
        try {
            $teacher = Auth::user();
            $question = $answer->question;
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому ответу');
            }

            return view('teacher.answers.show', compact('answer', 'question', 'test'));
        } catch (\Exception $e) {
            Log::error('Ошибка при просмотре ответа', [
                'error' => $e->getMessage(),
                'answer_id' => $answer->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при просмотре ответа');
        }
    }

    /**
     * Показать форму редактирования ответа.
     */
    public function edit(Answer $answer)
    {
        try {
            $teacher = Auth::user();
            $question = $answer->question;
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому ответу');
            }

            return view('teacher.answers.edit', compact('answer', 'question', 'test'));
        } catch (\Exception $e) {
            Log::error('Ошибка при редактировании ответа', [
                'error' => $e->getMessage(),
                'answer_id' => $answer->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при редактировании ответа');
        }
    }

    /**
     * Обновить ответ.
     */
    public function update(Request $request, Answer $answer)
    {
        try {
            $teacher = Auth::user();
            $question = $answer->question;
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому ответу');
            }

            // Проверяем, есть ли ответы студентов на этот вопрос
            if ($question->studentAnswers()->count() > 0) {
                return redirect()->back()->with('error', 'Нельзя редактировать ответы к вопросу, на который уже есть ответы студентов');
            }

            $validated = $request->validate([
                'answer_text' => 'required|string|max:1000',
                'is_correct' => 'boolean'
            ]);

            $answer->update([
                'answer_text' => $validated['answer_text'],
                'is_correct' => $validated['is_correct'] ?? false
            ]);

            Log::info('Ответ обновлен успешно', [
                'answer_id' => $answer->id,
                'question_id' => $question->id,
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.answers.show', $answer)
                ->with('success', 'Ответ успешно обновлен');

        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении ответа', [
                'error' => $e->getMessage(),
                'answer_id' => $answer->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при обновлении ответа');
        }
    }

    /**
     * Удалить ответ.
     */
    public function destroy(Answer $answer)
    {
        try {
            $teacher = Auth::user();
            $question = $answer->question;
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому ответу');
            }

            // Проверяем, есть ли ответы студентов на этот вопрос
            if ($question->studentAnswers()->count() > 0) {
                return redirect()->back()->with('error', 'Нельзя удалять ответы к вопросу, на который уже есть ответы студентов');
            }

            // Проверяем, что это не единственный ответ
            if ($question->answers()->count() <= 1) {
                return redirect()->back()->with('error', 'Нельзя удалить единственный ответ на вопрос');
            }

            $answer->delete();

            Log::info('Ответ удален успешно', [
                'answer_id' => $answer->id,
                'question_id' => $question->id,
                'test_id' => $test->id,
                'teacher_id' => Auth::id()
            ]);

            return redirect()->route('teacher.answers.index', $question)
                ->with('success', 'Ответ успешно удален');

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении ответа', [
                'error' => $e->getMessage(),
                'answer_id' => $answer->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при удалении ответа');
        }
    }

    /**
     * Показать статистику по ответу.
     */
    public function statistics(Answer $answer)
    {
        try {
            $teacher = Auth::user();
            $question = $answer->question;
            $test = $question->test;
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->teacher_id !== $teacher->id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому ответу');
            }

            // Статистика по ответу
            $totalAnswers = $question->studentAnswers()->count();
            $selectedCount = $question->studentAnswers()
                ->whereJsonContains('selected_answers', $answer->id)
                ->count();

            $statistics = [
                'total_answers' => $totalAnswers,
                'selected_count' => $selectedCount,
                'selection_percentage' => $totalAnswers > 0 ? round(($selectedCount / $totalAnswers) * 100, 2) : 0,
                'is_correct' => $answer->is_correct,
                'student_answers' => $question->studentAnswers()
                    ->whereJsonContains('selected_answers', $answer->id)
                    ->with('student')
                    ->paginate(20)
            ];

            return view('teacher.answers.statistics', compact('answer', 'question', 'test', 'statistics'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении статистики ответа', [
                'error' => $e->getMessage(),
                'answer_id' => $answer->id,
                'teacher_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении статистики');
        }
    }
}
