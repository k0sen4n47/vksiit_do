<?php

namespace App\Http\Controllers\Teacher\Assignment;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentFile;
use App\Models\AssignmentPage;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Group;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CreateController extends Controller
{
    /**
     * CreateController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\RoleMiddleware::class.':teacher');
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function index(Request $request)
    {
        $teacher = Auth::user();
        $subjects = $teacher->subjects;
        $groups = $teacher->groups;

        // Получаем предзаполненные значения из параметров
        $selectedSubjectId = $request->get('subjectId') ?: $request->get('subject');
        $selectedGroupId = $request->get('groupId') ?: $request->get('group');

        // Проверяем доступ учителя к выбранным предмету и группе
        $selectedSubject = null;
        $selectedGroup = null;

        if ($selectedSubjectId) {
            $selectedSubject = $subjects->find($selectedSubjectId);
            if (!$selectedSubject) {
                return redirect()->route('teacher.assignments.index')
                    ->with('error', 'У вас нет доступа к выбранному предмету');
            }
        }

        if ($selectedGroupId) {
            $selectedGroup = $groups->find($selectedGroupId);
            if (!$selectedGroup) {
                return redirect()->route('teacher.assignments.index')
                    ->with('error', 'У вас нет доступа к выбранной группе');
            }
        }

        return view('teacher.assignment.create.index', compact(
            'subjects', 
            'groups', 
            'selectedSubject', 
            'selectedGroup'
        ));
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Starting assignment save process', [
                'teacher_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Получаем данные из JSON или FormData
            $data = $request->isJson() ? $request->json()->all() : $request->all();

            // Валидация основных полей
            $validated = validator($data, [
                'subject_id' => 'required|exists:subjects,id',
                'group_id' => 'required|exists:groups,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'deadline' => 'required|date|after:now',
                'max_score' => 'required|integer|min:1',
                'pages' => 'required|array|min:1',
                'pages.*.type' => 'required|in:text,code,test',
                'pages.*.title' => 'required|string|max:255',
                'pages.*.content' => 'nullable|string',
                'pages.*.order' => 'required|integer|min:1'
            ])->validate();

            // Проверяем доступ учителя к предмету и группе
            $teacher = Auth::user();
            $subject = Subject::findOrFail($validated['subject_id']);
            $group = Group::findOrFail($validated['group_id']);

            if (!$teacher->subjects->contains($subject)) {
                Log::error('Teacher does not have access to the subject', [
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к выбранному предмету'
                ], 403);
            }

            if (!$teacher->groups->contains($group)) {
                Log::error('Teacher does not have access to the group', [
                    'teacher_id' => $teacher->id,
                    'group_id' => $group->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к выбранной группе'
                ], 403);
            }

            DB::beginTransaction();

            // Создаем задание
            $assignment = Assignment::create([
                'teacher_id' => $teacher->id,
                'subject_id' => $validated['subject_id'],
                'group_id' => $validated['group_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'deadline' => $validated['deadline'],
                'max_score' => $validated['max_score'],
                'status' => 'active'
            ]);

            // Обрабатываем файлы (если есть)
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('assignments/' . $assignment->id, 'public');
                    AssignmentFile::create([
                        'assignment_id' => $assignment->id,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName()
                    ]);
                }
            }

            // Создаем страницы задания
            foreach ($validated['pages'] as $pageData) {
                // Формируем контент в зависимости от типа страницы
                $content = [];
                
                switch ($pageData['type']) {
                    case 'text':
                        $content = [
                            'title' => $pageData['title'],
                            'text' => $pageData['content'] ?? '',
                            'images' => []
                        ];
                        break;
                        
                    case 'code':
                        $html = '';
                        $css = '';
                        if (!empty($pageData['content'])) {
                            $decoded = json_decode($pageData['content'], true);
                            if (is_array($decoded)) {
                                $html = $decoded['html'] ?? '';
                                $css = $decoded['css'] ?? '';
                            }
                        }
                        $content = [
                            'title' => $pageData['title'],
                            'html' => $html,
                            'css' => $css,
                            'language' => 'htmlmixed',
                            'description' => $pageData['description'] ?? ''
                        ];
                        break;
                        
                    case 'test':
                        $content = [
                            'title' => $pageData['title'],
                            'description' => $pageData['content'] ?? '',
                            'time_limit' => null,
                            'passing_score' => 60
                        ];
                        break;
                        
                    default:
                        $content = [
                            'title' => $pageData['title'],
                            'content' => $pageData['content'] ?? ''
                        ];
                }

                $page = AssignmentPage::create([
                    'assignment_id' => $assignment->id,
                    'title' => $pageData['title'],
                    'content' => $content,
                    'type' => $pageData['type'],
                    'order' => $pageData['order']
                ]);

                // Если это тестовая страница, логируем и создаем тест
                if ($pageData['type'] === 'test') {
                    Log::info('DEBUG_TEST_PAGE_DATA', [
                        'pageData' => $pageData
                    ]);
                    if (!isset($pageData['test']) || empty($pageData['test']['questions'])) {
                        Log::error('Test page missing questions', [
                            'pageData' => $pageData
                        ]);
                    } else {
                        $this->createTestForPage($page, $pageData['test']);
                    }
                }
            }

            DB::commit();

            Log::info('Assignment created successfully', [
                'assignment_id' => $assignment->id,
                'teacher_id' => $teacher->id,
                'pages_count' => count($validated['pages'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Задание успешно создано',
                'assignment_id' => $assignment->id,
                'redirect_url' => route('teacher.assignments.show', $assignment)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error creating assignment', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации данных',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating assignment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при создании задания: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Создать тест для страницы.
     */
    private function createTestForPage(AssignmentPage $page, array $testData): void
    {
        // Валидация данных теста
        $validated = validator($testData, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'shuffle_questions' => 'boolean',
            'show_results' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:single,multiple,text',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.answers' => 'required_if:questions.*.type,single,multiple|array',
            'questions.*.answers.*.answer_text' => 'required_if:questions.*.type,single,multiple|string',
            'questions.*.answers.*.is_correct' => 'boolean'
        ])->validate();

        // Создаем тест
        $test = Test::create([
            'assignment_page_id' => $page->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'time_limit' => $validated['time_limit'] ?? null,
            'passing_score' => $validated['passing_score'],
            'max_attempts' => $validated['max_attempts'] ?? null,
            'shuffle_questions' => $validated['shuffle_questions'] ?? false,
            'show_results' => $validated['show_results'] ?? true,
            'is_active' => true
        ]);

        // Создаем вопросы и ответы
        foreach ($validated['questions'] as $questionData) {
            $question = Question::create([
                'test_id' => $test->id,
                'question_text' => $questionData['question_text'],
                'type' => $questionData['type'],
                'points' => $questionData['points']
            ]);

            // Создаем ответы для закрытых вопросов
            if (in_array($questionData['type'], ['single', 'multiple']) && isset($questionData['answers'])) {
                foreach ($questionData['answers'] as $answerData) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => $answerData['answer_text'],
                        'is_correct' => $answerData['is_correct'] ?? false
                    ]);
                }
            }
        }

        Log::info('Test created for assignment page', [
            'test_id' => $test->id,
            'page_id' => $page->id,
            'questions_count' => count($validated['questions'])
        ]);
    }
} 