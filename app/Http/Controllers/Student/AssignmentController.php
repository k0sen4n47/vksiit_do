<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentPage;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\StudentAnswer;
use App\Models\AssignmentStudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\RoleMiddleware::class.':student');
    }

    /**
     * Показать список заданий для студента.
     */
    public function index()
    {
        try {
            $student = Auth::user();
            
            $assignments = Assignment::where('group_id', $student->group_id)
                ->where('status', 'active')
                ->with(['subject', 'teacher', 'pages'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('student.assignments.index', compact('assignments'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении списка заданий для студента', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении списка заданий');
        }
    }

    /**
     * Показать задание для студента.
     */
    public function show(Assignment $assignment)
    {
        try {
            $student = Auth::user();
            // DEBUG LOG
            \Log::info('DEBUG_ASSIGNMENT_ACCESS', [
                'student_id' => $student->id,
                'student_group_id' => $student->group_id,
                'assignment_id' => $assignment->id,
                'assignment_group_id' => $assignment->group_id,
                'assignment_status' => $assignment->status,
            ]);
            // Проверяем доступ к заданию
            if ($assignment->group_id !== $student->group_id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому заданию');
            }

            $assignment->load(['subject', 'teacher', 'pages.test.questions.answers']);

            // Получаем результаты тестирования студента
            $testResults = TestResult::where('student_id', $student->id)
                ->whereHas('test.assignmentPage', function ($query) use ($assignment) {
                    $query->where('assignment_id', $assignment->id);
                })->with(['test.assignmentPage'])
                ->get();

            // Получаем ответ студента на это задание (если есть)
            $studentAnswer = $assignment->answers()
                ->where('student_id', $student->id)
                ->first();

            return view('student.assignments.show', compact('assignment', 'testResults', 'studentAnswer'));
        } catch (\Exception $e) {
            Log::error('Ошибка при просмотре задания студентом', [
                'error' => $e->getMessage(),
                'assignment_id' => $assignment->id,
                'student_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при просмотре задания');
        }
    }

    /**
     * Начать прохождение теста.
     */
    public function startTest(Test $test)
    {
        try {
            $student = Auth::user();
            
            // Проверяем доступ к тесту
            if ($test->assignmentPage->assignment->group_id !== $student->group_id) {
                return redirect()->back()->with('error', 'У вас нет доступа к этому тесту');
            }

            // Проверяем, не превышено ли количество попыток
            $attemptsCount = TestResult::where('test_id', $test->id)
                ->where('student_id', $student->id)
                ->count();

            if ($test->max_attempts && $attemptsCount >= $test->max_attempts) {
                return redirect()->back()->with('error', 'Вы исчерпали все попытки прохождения этого теста');
            }

            // Создаем новый результат тестирования
            $testResult = TestResult::create([
                'test_id' => $test->id,
                'student_id' => $student->id,
                'score' => 0,
                'max_score' => $test->max_score,
                'started_at' => now(),
                'status' => 'in_progress'
            ]);

            Log::info('Студент начал прохождение теста', [
                'test_result_id' => $testResult->id,
                'test_id' => $test->id,
                'student_id' => $student->id
            ]);

            return redirect()->route('student.tests.take', $testResult);

        } catch (\Exception $e) {
            Log::error('Ошибка при начале прохождения теста', [
                'error' => $e->getMessage(),
                'test_id' => $test->id,
                'student_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при начале прохождения теста');
        }
    }

    /**
     * Показать результаты тестирования студента.
     */
    public function testResults()
    {
        try {
            $student = Auth::user();
            
            $testResults = TestResult::where('student_id', $student->id)
                ->with(['test.assignmentPage.assignment.subject', 'test.assignmentPage.assignment.teacher'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('student.assignments.test-results', compact('testResults'));
        } catch (\Exception $e) {
            Log::error('Ошибка при получении результатов тестирования', [
                'error' => $e->getMessage(),
                'student_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при получении результатов');
        }
    }

    /**
     * Показать детальный результат тестирования.
     */
    public function testResult(TestResult $testResult)
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

            return view('student.assignments.test-result', compact('testResult'));
        } catch (\Exception $e) {
            Log::error('Ошибка при просмотре результата тестирования', [
                'error' => $e->getMessage(),
                'test_result_id' => $testResult->id,
                'student_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Произошла ошибка при просмотре результата');
        }
    }

    /**
     * Сохранить ответ студента на задание.
     */
    public function answer(Request $request, Assignment $assignment)
    {
        try {
            $student = Auth::user();
            
            // Проверяем доступ к заданию
            if ($assignment->group_id !== $student->group_id) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'У вас нет доступа к этому заданию'], 403);
                }
                return redirect()->back()->with('error', 'У вас нет доступа к этому заданию');
            }

            // Валидация данных
            $validated = $request->validate([
                'page_id' => 'required|exists:assignment_pages,id',
                'answer_text' => 'nullable|string',
                'answer_html' => 'nullable|string',
                'answer_css' => 'nullable|string',
            ]);

            // Дополнительная валидация файлов (без использования fileinfo)
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $index => $file) {
                    if ($file) {
                        // Проверяем размер файла
                        if ($file->getSize() > 10240 * 1024) { // 10MB в байтах
                            throw new \Illuminate\Validation\ValidationException(
                                validator([], []),
                                response()->json([
                                    'success' => false,
                                    'message' => "Файл {$file->getClientOriginalName()} слишком большой. Максимальный размер: 10MB"
                                ], 422)
                            );
                        }
                        
                        // Проверяем, что файл действительно загружен
                        if (!$file->isValid()) {
                            throw new \Illuminate\Validation\ValidationException(
                                validator([], []),
                                response()->json([
                                    'success' => false,
                                    'message' => "Ошибка при загрузке файла {$file->getClientOriginalName()}"
                                ], 422)
                            );
                        }
                    }
                }
            }

            // Проверяем, что страница принадлежит заданию
            $page = AssignmentPage::where('id', $validated['page_id'])
                ->where('assignment_id', $assignment->id)
                ->firstOrFail();

            // Сохраняем файлы, если они есть
            $savedFiles = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $index => $file) {
                    try {
                        // Дополнительная проверка, что это действительно файл
                        if ($file && $file->isValid()) {
                            // Пытаемся сохранить файл
                            try {
                                $path = $file->store('student-answers/' . $assignment->id . '/' . $student->id, 'public');
                                $savedFiles[] = $path;
                                
                                Log::info('Файл успешно сохранен', [
                                    'file_index' => $index,
                                    'original_name' => $file->getClientOriginalName(),
                                    'saved_path' => $path,
                                    'size' => $file->getSize()
                                ]);
                            } catch (\Exception $storeException) {
                                // Если не удалось сохранить из-за MIME-типа, пробуем альтернативный способ
                                if (strpos($storeException->getMessage(), 'MIME type') !== false) {
                                    $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
                                    $filename = uniqid() . '.' . $extension;
                                    $path = 'student-answers/' . $assignment->id . '/' . $student->id . '/' . $filename;
                                    
                                    // Сохраняем файл вручную
                                    $file->move(storage_path('app/public/student-answers/' . $assignment->id . '/' . $student->id), $filename);
                                    $savedFiles[] = $path;
                                    
                                    Log::info('Файл сохранен альтернативным способом', [
                                        'file_index' => $index,
                                        'original_name' => $file->getClientOriginalName(),
                                        'saved_path' => $path,
                                        'size' => $file->getSize()
                                    ]);
                                } else {
                                    throw $storeException;
                                }
                            }
                        } else {
                            Log::warning('Некорректный файл', [
                                'file_index' => $index,
                                'is_file' => $file ? 'yes' : 'no',
                                'is_valid' => $file ? ($file->isValid() ? 'yes' : 'no') : 'n/a'
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Ошибка при сохранении файла', [
                            'file_index' => $index,
                            'error' => $e->getMessage(),
                            'file_name' => $file ? $file->getClientOriginalName() : 'unknown'
                        ]);
                        
                        // Возвращаем ошибку пользователю
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => false,
                                'message' => "Ошибка при сохранении файла {$file->getClientOriginalName()}: " . $e->getMessage()
                            ], 422);
                        }
                        
                        return redirect()->back()->with('error', "Ошибка при сохранении файла {$file->getClientOriginalName()}");
                    }
                }
            }

            // Создаем или обновляем ответ
            $studentAnswer = AssignmentStudentAnswer::updateOrCreate(
                [
                    'assignment_page_id' => $validated['page_id'],
                    'student_id' => $student->id,
                ],
                [
                    'answer_text' => $validated['answer_text'] ?? null,
                    'answer_html' => $validated['answer_html'] ?? null,
                    'answer_css' => $validated['answer_css'] ?? null,
                    'files' => !empty($savedFiles) ? json_encode($savedFiles) : null,
                    'submitted_at' => now(),
                ]
            );

            // Если задание еще активно, меняем его статус на completed после отправки ответа
            if ($assignment->status === 'active') {
                $assignment->markAsCompleted();
                Log::info('Задание отмечено как выполненное после отправки ответа', [
                    'assignment_id' => $assignment->id,
                    'student_id' => $student->id,
                    'page_id' => $validated['page_id']
                ]);
            }

            Log::info('Ответ студента сохранен', [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'page_id' => $validated['page_id'],
                'has_text' => !empty($validated['answer_text']),
                'files_count' => count($savedFiles),
                'assignment_status' => $assignment->fresh()->status
            ]);

            // Возвращаем JSON для AJAX запросов
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ответ успешно отправлен!'
                ]);
            }

            return redirect()->back()->with('success', 'Ответ успешно отправлен!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Ошибка валидации при сохранении ответа студента', [
                'errors' => $e->errors(),
                'assignment_id' => $assignment->id,
                'student_id' => Auth::id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации данных',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Ошибка при сохранении ответа студента', [
                'error' => $e->getMessage(),
                'assignment_id' => $assignment->id,
                'student_id' => Auth::id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Произошла ошибка при сохранении ответа'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Произошла ошибка при сохранении ответа');
        }
    }
} 