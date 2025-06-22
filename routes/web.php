<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\GroupNameComponentController;
use App\Http\Controllers\Teacher\Assignment\CreateController;
use App\Http\Controllers\Teacher\Assignment\ShowController; 
use App\Http\Controllers\Teacher\Assignment\EditController;
use App\Http\Controllers\Teacher\Assignment\DeleteController;
use App\Http\Controllers\Teacher\Assignment\ListController;
use App\Http\Controllers\Teacher\Assignment\SubjectController as TeacherAssignmentSubjectController;
use App\Http\Controllers\Teacher\Assignment\GroupController as TeacherAssignmentGroupController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Teacher\Assignment\IndexController;
use App\Http\Controllers\Teacher\TestController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\AnswerController;
use App\Http\Controllers\Teacher\TestResultController;
use App\Http\Controllers\Teacher\StudentAnswerController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Маршруты аутентификации
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Маршруты для админ-панели
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\RoleMiddleware::class.':admin'])->group(function () {
    // Главная страница админки
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Маршруты для групп
    Route::get('/groups/create', [GroupController::class, 'createGroup'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'storeGroup'])->name('groups.store');
    Route::get('/groups', [GroupController::class, 'indexGroups'])->name('groups.index');
    Route::get('/groups/{group}/edit', [GroupController::class, 'editGroup'])->name('groups.edit');
    Route::put('/groups/{group}', [GroupController::class, 'updateGroup'])->name('groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroyGroup'])->name('groups.destroy');

    // Маршруты для студентов
    Route::get('/students/create', [StudentController::class, 'createStudent'])->name('students.create');
    Route::post('/students', [StudentController::class, 'storeStudent'])->name('students.store');
    Route::get('/students', [StudentController::class, 'indexStudents'])->name('students.index');
    Route::get('/students/{student}/edit', [StudentController::class, 'editStudent'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroyStudent'])->name('students.destroy');

    // Маршруты для преподавателей
    Route::get('/teachers/create', [TeacherController::class, 'createTeacher'])->name('teachers.create');
    Route::post('/teachers', [TeacherController::class, 'storeTeacher'])->name('teachers.store');
    Route::get('/teachers', [TeacherController::class, 'indexTeachers'])->name('teachers.index');
    Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'editTeacher'])->name('teachers.edit');
    Route::put('/teachers/{teacher}', [TeacherController::class, 'updateTeacher'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroyTeacher'])->name('teachers.destroy');

    // Маршруты для предметов
    Route::get('/subjects/create', [SubjectController::class, 'createSubject'])->name('subjects.create');
    Route::post('/subjects', [SubjectController::class, 'storeSubject'])->name('subjects.store');
    Route::get('/subjects', [SubjectController::class, 'indexSubjects'])->name('subjects.index');
    Route::get('/subjects/{subject}/edit', [SubjectController::class, 'editSubject'])->name('subjects.edit');
    Route::put('/subjects/{subject}', [SubjectController::class, 'updateSubject'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroySubject'])->name('subjects.destroy');

    // Маршруты для заданий
    Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

    // Маршруты для управления компонентами названий групп
    Route::resource('group-name-components', GroupNameComponentController::class);
});

// Маршруты для личного кабинета студента
Route::prefix('student')->name('student.')->middleware(['auth', \App\Http\Middleware\RoleMiddleware::class.':student'])->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    
    // Маршруты для предметов
    Route::get('/subjects', [\App\Http\Controllers\Student\SubjectController::class, 'index'])->name('subjects.index');
    Route::get('/subjects/{subject}', [\App\Http\Controllers\Student\SubjectController::class, 'show'])->name('subjects.show');
    
    // Маршруты для заданий
    Route::prefix('assignments')->name('assignments.')->group(function () {
        Route::get('/', [App\Http\Controllers\Student\AssignmentController::class, 'index'])->name('index');
        Route::get('/{assignment}', [App\Http\Controllers\Student\AssignmentController::class, 'show'])->name('show');
        Route::post('/{assignment}/answer', [App\Http\Controllers\Student\AssignmentController::class, 'answer'])->name('answer');
        Route::get('/{assignment}/test-results', [App\Http\Controllers\Student\AssignmentController::class, 'testResults'])->name('test-results');
    });

    // Маршруты для тестов
    Route::prefix('tests')->name('tests.')->group(function () {
        Route::get('/take/{testResult}', [App\Http\Controllers\Student\TestController::class, 'take'])->name('take');
        Route::post('/save-answers/{testResult}', [App\Http\Controllers\Student\TestController::class, 'saveAnswers'])->name('save-answers');
        Route::post('/finish/{testResult}', [App\Http\Controllers\Student\TestController::class, 'finish'])->name('finish');
        Route::get('/result/{testResult}', [App\Http\Controllers\Student\TestController::class, 'result'])->name('result');
    });

    // Маршруты для начала тестов
    Route::post('/start-test/{test}', [App\Http\Controllers\Student\AssignmentController::class, 'startTest'])->name('start-test');

    Route::post('/upload-photo', [\App\Http\Controllers\StudentController::class, 'uploadPhoto'])->name('upload-photo');
});

// Маршруты для личного кабинета преподавателя
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class.':teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\TeacherController::class, 'dashboard'])->name('dashboard');
    Route::post('/upload-photo', [\App\Http\Controllers\TeacherController::class, 'uploadPhoto'])->name('upload-photo');
    
    // Маршруты для заданий
    Route::prefix('assignments')->name('assignments.')->group(function () {
        // Список заданий
        Route::get('/', [ListController::class, 'index'])->name('index');
        
        // ВАЖНО: сначала маршруты для просмотра и редактирования
        Route::get('/view/{assignment}', [ShowController::class, 'show'])->name('show');
        Route::get('/edit/{assignment}', [EditController::class, 'edit'])->name('edit');
        
        // Потом маршрут с двумя параметрами
        Route::get('/{subjectId}/{groupId}', [IndexController::class, 'index'])->name('subject-group');
        
        // Выбор предмета и группы
        Route::get('/subjects', [TeacherAssignmentSubjectController::class, 'index'])->name('subjects.index');
        Route::get('/subjects/{subjectId}/groups', [TeacherAssignmentGroupController::class, 'index'])->name('groups.index');
        
        // Создание задания
        Route::get('/create', [CreateController::class, 'index'])->name('create');
        Route::post('/store', [CreateController::class, 'store'])->name('store');
        
        // Редактирование задания (PUT)
        Route::put('/update/{assignment}', [EditController::class, 'update'])->name('update');
        
        // Удаление задания
        Route::delete('/delete/{assignment}', [DeleteController::class, 'destroy'])->name('destroy');

        // Маршруты для создания страниц
        Route::get('/create/code-page', function (Request $request) {
            return view('teacher.assignment.create.code-page', [
                'pageIndex' => $request->query('pageIndex', 0)
            ]);
        })->name('create.code-page');

        Route::get('/create/test-page-template', function (Request $request) {
            return view('teacher.assignment.create.tests.page', [
                'pageIndex' => $request->query('pageIndex', 0)
            ]);
        })->name('create.test-page-template');

        Route::get('/create/test-question-template', function (Request $request) {
            return view('teacher.assignment.create.tests.partials.question', [
                'pageIndex' => $request->query('pageIndex', 0),
                'questionIndex' => $request->query('questionIndex', 0)
            ]);
        })->name('create.test-question-template');

        Route::get('/create/test-answer-template', function (Request $request) {
            return view('teacher.assignment.create.tests.partials.answer', [
                'pageIndex' => $request->query('pageIndex', 0),
                'questionIndex' => $request->query('questionIndex', 0),
                'answerIndex' => $request->query('answerIndex', 0)
            ]);
        })->name('create.test-answer-template');

        // Просмотр одного ответа студента на задание
        Route::get('/view/{assignment}/answer/{answer}', [ShowController::class, 'studentAnswerShow'])->name('student-answer.show');
    });

    // Маршруты для тестов
    Route::prefix('tests')->name('tests.')->group(function () {
        Route::get('/', [TestController::class, 'index'])->name('index');
        Route::get('/create/{assignmentPage}', [TestController::class, 'create'])->name('create');
        Route::post('/store/{assignmentPage}', [TestController::class, 'store'])->name('store');
        Route::get('/{test}', [TestController::class, 'show'])->name('show');
        Route::get('/{test}/edit', [TestController::class, 'edit'])->name('edit');
        Route::put('/{test}', [TestController::class, 'update'])->name('update');
        Route::delete('/{test}', [TestController::class, 'destroy'])->name('destroy');
        Route::get('/{test}/results', [TestController::class, 'results'])->name('results');
        Route::get('/{test}/statistics', [TestController::class, 'statistics'])->name('statistics');
        Route::get('/{test}/export', [TestController::class, 'export'])->name('export');
    });

    // Маршруты для вопросов
    Route::prefix('questions')->name('questions.')->group(function () {
        Route::get('/{test}', [QuestionController::class, 'index'])->name('index');
        Route::get('/{test}/create', [QuestionController::class, 'create'])->name('create');
        Route::post('/{test}/store', [QuestionController::class, 'store'])->name('store');
        Route::get('/question/{question}', [QuestionController::class, 'show'])->name('show');
        Route::get('/question/{question}/edit', [QuestionController::class, 'edit'])->name('edit');
        Route::put('/question/{question}', [QuestionController::class, 'update'])->name('update');
        Route::delete('/question/{question}', [QuestionController::class, 'destroy'])->name('destroy');
        Route::get('/question/{question}/statistics', [QuestionController::class, 'statistics'])->name('statistics');
    });

    // Маршруты для ответов
    Route::prefix('answers')->name('answers.')->group(function () {
        Route::get('/{question}', [AnswerController::class, 'index'])->name('index');
        Route::get('/{question}/create', [AnswerController::class, 'create'])->name('create');
        Route::post('/{question}/store', [AnswerController::class, 'store'])->name('store');
        Route::get('/answer/{answer}', [AnswerController::class, 'show'])->name('show');
        Route::get('/answer/{answer}/edit', [AnswerController::class, 'edit'])->name('edit');
        Route::put('/answer/{answer}', [AnswerController::class, 'update'])->name('update');
        Route::delete('/answer/{answer}', [AnswerController::class, 'destroy'])->name('destroy');
        Route::get('/answer/{answer}/statistics', [AnswerController::class, 'statistics'])->name('statistics');
    });

    // Маршруты для результатов тестирования
    Route::prefix('test-results')->name('test-results.')->group(function () {
        Route::get('/', [TestResultController::class, 'index'])->name('index');
        Route::get('/{testResult}', [TestResultController::class, 'show'])->name('show');
        Route::get('/{testResult}/edit', [TestResultController::class, 'edit'])->name('edit');
        Route::put('/{testResult}', [TestResultController::class, 'update'])->name('update');
        Route::delete('/{testResult}', [TestResultController::class, 'destroy'])->name('destroy');
        Route::get('/test/{test}/results', [TestResultController::class, 'testResults'])->name('test-results');
        Route::get('/student/{student}/results', [TestResultController::class, 'studentResults'])->name('student-results');
        Route::get('/test/{test}/export', [TestResultController::class, 'export'])->name('export');
    });

    // Маршруты для ответов студентов
    Route::prefix('student-answers')->name('student-answers.')->group(function () {
        Route::get('/{question}', [StudentAnswerController::class, 'index'])->name('index');
        Route::get('/answer/{studentAnswer}', [StudentAnswerController::class, 'show'])->name('show');
        Route::get('/answer/{studentAnswer}/edit', [StudentAnswerController::class, 'edit'])->name('edit');
        Route::put('/answer/{studentAnswer}', [StudentAnswerController::class, 'update'])->name('update');
        Route::delete('/answer/{studentAnswer}', [StudentAnswerController::class, 'destroy'])->name('destroy');
        Route::get('/test-result/{testResult}/answers', [StudentAnswerController::class, 'studentTestAnswers'])->name('student-test-answers');
        Route::get('/{question}/bulk-review', [StudentAnswerController::class, 'bulkReview'])->name('bulk-review');
        Route::post('/{question}/bulk-review', [StudentAnswerController::class, 'bulkReviewStore'])->name('bulk-review-store');
    });

    Route::post('/assignments/upload-image', [AssignmentController::class, 'uploadImage'])->name('assignments.upload-image');

    Route::patch('/assignments/{assignment}/status', [App\Http\Controllers\Teacher\Assignment\EditController::class, 'updateStatus'])->name('teacher.assignments.updateStatus');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::get('/editor', function () {
    return view('editor.index');
})->name('editor');

Route::post('/password/forgot/login', [ForgotPasswordController::class, 'sendResetLinkByLogin'])->name('password.forgot.login');

Route::get('/password/forgot', function() {
    return view('auth.passwords.forgot');
})->name('password.forgot');

Route::post('/password/email', function(Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink(
        $request->only('email')
    );
    return back()->with('status', __($status));
})->name('password.email');

Route::get('/password/reset/{token}', function ($token) {
    $email = request('email');
    return view('auth.passwords.reset', ['token' => $token, 'email' => $email]);
})->name('password.reset');

Route::post('/password/reset', function (Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => [
            'required',
            'confirmed',
            'min:8',
            'regex:/[a-zA-Z]/', // хотя бы одна буква
            'regex:/[0-9]/',    // хотя бы одна цифра
        ],
    ], [
        'password.min' => 'Пароль должен содержать минимум 8 символов.',
        'password.regex' => 'Пароль должен содержать хотя бы одну букву и одну цифру.',
        'password.confirmed' => 'Пароли не совпадают.',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
})->name('password.update');
