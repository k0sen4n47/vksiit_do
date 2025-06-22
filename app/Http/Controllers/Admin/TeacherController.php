<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
// use App\Http\Controllers\Admin\StudentController;
use Illuminate\Support\Facades\Auth;
use App\Services\HelperService;
use App\Mail\SendCredentialsMail;
use Illuminate\Support\Facades\Mail;

class TeacherController extends Controller
{
    /**
     * Отображает форму создания нового преподавателя.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function createTeacher()
    {
        return view('admin.teacher.create');
    }

    /**
     * Сохраняет нового преподавателя в базе данных.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTeacher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fio' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $validator->validated();

        // Генерация логина
        $fioParts = explode(' ', trim($data['fio']));
        $lastName = HelperService::transliterateCyrillicToLatin(array_shift($fioParts));
        $firstNameInitial = !empty($fioParts) ? HelperService::transliterateCyrillicToLatin(mb_substr(array_shift($fioParts), 0, 1, 'UTF-8')) : '';

        $login = strtolower($firstNameInitial . $lastName);

        // Проверка на уникальность логина и добавление числа при необходимости
        $originalLogin = $login;
        $count = 1;
        while (User::where('login', $login)->exists()) {
            $login = $originalLogin . $count++;
        }

        // Генерация пароля из части email до "@"
        $emailParts = explode('@', $data['email']);
        $generatedPassword = $emailParts[0];
        $password = Hash::make($generatedPassword);

        User::create([
            'fio' => $data['fio'],
            'name' => $data['fio'],
            'email' => $data['email'],
            'role' => 'teacher',
            'login' => $login,
            'password' => $password,
        ]);

        // Отправка письма с логином и паролем
        Mail::to($data['email'])->send(new SendCredentialsMail($login, $generatedPassword));

        return redirect()->route('admin.teachers.index')->with('success', 'Преподаватель успешно создан!');
    }

    /**
     * Отображает список всех преподавателей с возможностью фильтрации.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function indexTeachers(Request $request)
    {
        $query = User::where('role', 'teacher');

        // Фильтр по поиску по тексту (ФИО или Email)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('fio', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $teachers = $query->get();

        return view('admin.teacher.index', ['teachers' => $teachers, 'request' => $request]);
    }

    /**
     * Отображает форму редактирования преподавателя.
     *
     * @param  \App\Models\User  $teacher
     * @return \Illuminate\Contracts\View\View
     */
    public function editTeacher(User $teacher)
    {
        // Проверяем, что пользователь действительно преподаватель
        if ($teacher->role !== 'teacher') {
            abort(404); // Или другое подходящее действие
        }

        return view('admin.teacher.edit', compact('teacher'));
    }

    /**
     * Обновляет данные преподавателя в базе данных.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $teacher
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTeacher(Request $request, User $teacher)
    {
        // Проверяем, что пользователь действительно преподаватель
        if ($teacher->role !== 'teacher') {
            abort(404); // Или другое подходящее действие
        }

        $validator = Validator::make($request->all(), [
            'fio' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $teacher->update([
            'fio' => $validator->validated()['fio'],
            'name' => $validator->validated()['fio'],
            'email' => $validator->validated()['email'],
        ]);

        return redirect()->route('admin.teachers.index')->with('success', 'Данные преподавателя успешно обновлены!');
    }

    /**
     * Удаляет преподавателя из базы данных.
     *
     * @param  \App\Models\User  $teacher
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyTeacher(User $teacher)
    {
        // Проверяем, что пользователь действительно преподаватель
        if ($teacher->role !== 'teacher') {
            abort(404); // Или другое подходящее действие
        }

        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Преподаватель успешно удален!');
    }

    /**
     * Отображает главную страницу личного кабинета преподавателя.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        // Здесь можно получить данные, специфичные для преподавателя
        $teacher = Auth::user(); // Получаем текущего авторизованного преподавателя

        return view('teacher.dashboard', compact('teacher'));
    }
} 