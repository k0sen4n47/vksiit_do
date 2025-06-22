<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Services\HelperService;
use App\Mail\SendCredentialsMail;
use Illuminate\Support\Facades\Mail;

class StudentController extends Controller
{
    /**
     * Отображает форму создания нового студента.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function createStudent()
    {
        // Получаем список всех групп для выпадающего списка
        $groups = Group::all();

        // Возвращаем представление формы создания студента, передавая список групп
        return view('admin.student.create', compact('groups'));
    }

    /**
     * Сохраняет нового студента в базе данных.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeStudent(Request $request)
    {
        // Валидация данных формы
        $validator = Validator::make($request->all(), [
            'fio' => 'required|string|max:255', // ФИО обязательно
            'email' => 'required|email|unique:users,email', // Email обязателен, в формате email и уникален
            'group_id' => 'required|exists:groups,id', // Группа обязательна и должна существовать в таблице groups
            // Правило валидации для subgroup при создании (может быть null)
            'subgroup' => ['nullable', Rule::in(['first', 'second'])],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Получаем выбранную группу
        $group = Group::findOrFail($data['group_id']);

        // --- Проверка ограничения на количество студентов в группе ---
        $totalStudentsInGroup = $group->students()->count();
        if ($totalStudentsInGroup >= 30) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['group_id' => 'В этой группе уже максимальное количество студентов (30).']);
        }
        // -----------------------------------------------------------

        // --- Логика автоматического назначения подгруппы при создании ---
        $firstSubgroupCount = $group->students()->where('subgroup', 'first')->count();
        $secondSubgroupCount = $group->students()->where('subgroup', 'second')->count();

        $assignedSubgroup = null;

        if ($firstSubgroupCount < 15) {
            $assignedSubgroup = 'first';
        } elseif ($secondSubgroupCount < 15) {
            $assignedSubgroup = 'second';
        }
        // Если в обеих подгруппах по 15 или более студентов, assignedSubgroup останется null

        // Переопределяем subgroup в $data, если оно было автоматически назначено
        $data['subgroup'] = $assignedSubgroup;
        // ------------------------------------------------------------

        // Генерация логина
        $shortNamePart = HelperService::transliterateCyrillicToLatin($group->short_name); // Используем метод транслитерации
        $fioParts = explode(' ', trim($data['fio']));
        $lastName = HelperService::transliterateCyrillicToLatin(array_shift($fioParts));
        $firstLetterName = !empty($fioParts) ? HelperService::transliterateCyrillicToLatin(mb_substr(array_shift($fioParts), 0, 1, 'UTF-8')) : '';
        $firstLetterMiddleName = !empty($fioParts) ? HelperService::transliterateCyrillicToLatin(mb_substr(array_shift($fioParts), 0, 1, 'UTF-8')) : '';

        // Определяем порядковый номер студента в группе (текущее количество + 1)
        $sequentialNumber = $totalStudentsInGroup + 1;

        // Формируем логин с порядковым номером
        $login = strtolower($shortNamePart . '-' . $sequentialNumber);

        // Проверка на уникальность логина и добавление числа при необходимости (этот шаг можно оставить как запасной)
        $originalLogin = $login;
        $count = 1;
        while (User::where('login', $login)->exists()) {
             // В случае конфликта (очень маловероятно с порядковым номером), добавляем еще один счетчик
            $login = $originalLogin . '_' . $count++;
        }

        // Генерация пароля из части email до "@"
        $emailParts = explode('@', $data['email']);
        $generatedPassword = $emailParts[0];
        $password = Hash::make($generatedPassword);

        // Создание нового пользователя с ролью 'student' и подгруппой
        User::create([
            'fio' => $data['fio'],
            'name' => $data['fio'], // Добавляем поле 'name'
            'email' => $data['email'],
            'group_id' => $data['group_id'],
            'role' => 'student',
            'login' => $login,
            'password' => $password,
            'subgroup' => $data['subgroup'], // Сохраняем назначенную подгруппу
        ]);

        // Отправка письма с логином и паролем
        Mail::to($data['email'])->send(new SendCredentialsMail($login, $generatedPassword));

        // Перенаправление после успешного сохранения
        return redirect()->route('admin.students.index')->with('success', 'Студент успешно создан!');
    }

    /**
     * Отображает список всех студентов с возможностью фильтрации.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function indexStudents(Request $request)
    {
        // Начинаем запрос к пользователям с ролью 'student' и загрузкой группы
        $query = User::where('role', 'student')->with('group');

        // Фильтр по поиску по тексту (ФИО или Email)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('fio', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Фильтр по группе
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->input('group_id'));
        }

        // --- Фильтр по подгруппе ---
        if ($request->filled('subgroup')) {
            $subgroup = $request->input('subgroup');
            // Если выбрана подгруппа ('first' или 'second'), фильтруем по ней
            if (in_array($subgroup, ['first', 'second'])) {
                $query->where('subgroup', $subgroup);
            } elseif ($subgroup === 'none') {
                // Если выбрано 'Без подгруппы', фильтруем по null
                $query->whereNull('subgroup');
            }
        }
        // --------------------------

        // Получаем отфильтрованных студентов
        $students = $query->get();

        // Получаем список всех групп для фильтрации
        $groups = Group::all();

        // Передаем в представление данные студентов, список групп и текущие параметры фильтрации
        return view('admin.student.index', ['students' => $students, 'groups' => $groups, 'request' => $request]);
    }

    /**
     * Отображает форму редактирования студента.
     *
     * @param  \App\Models\User  $student
     * @return \Illuminate\Contracts\View\View
     */
    public function editStudent(User $student)
    {
        // Проверяем, что пользователь действительно студент
        if ($student->role !== 'student') {
            abort(404); // Или другое подходящее действие, если пользователь не студент
        }

        // Получаем список всех групп для выпадающего списка
        $groups = Group::all();

        // Возвращаем представление формы редактирования студента, передавая данные студента и список групп
        return view('admin.student.edit', compact('student', 'groups'));
    }

    /**
     * Обновляет данные студента в базе данных.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStudent(Request $request, User $student)
    {
        // Проверяем, что пользователь действительно студент
        if ($student->role !== 'student') {
            abort(404); // Или другое подходящее действие
        }

        // Валидация данных формы при обновлении
        $validator = Validator::make($request->all(), [
            'fio' => 'required|string|max:255', // ФИО обязательно
            'email' => 'required|email|unique:users,email,' . $student->id, // Email обязателен, в формате email и уникален, игнорируя текущего студента
            'group_id' => 'required|exists:groups,id', // Группа обязательна и должна существовать в таблице groups
            // Правило валидации для subgroup при обновлении (может быть null или first/second)
            'subgroup' => ['nullable', Rule::in(['first', 'second'])],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Обновление данных студента, включая подгруппу
        $student->update([
            'fio' => $data['fio'],
            'name' => $data['fio'], // Обновляем name, чтобы оно соответствовало fio
            'email' => $data['email'],
            'group_id' => $data['group_id'],
            'subgroup' => $data['subgroup'], // Сохраняем подгруппу из формы (ручное изменение)
        ]);

        // Перенаправление после успешного обновления
        return redirect()->route('admin.students.index')->with('success', 'Данные студента успешно обновлены!');
    }

    /**
     * Удаляет студента из базы данных.
     *
     * @param  \App\Models\User  $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyStudent(User $student)
    {
        // Проверяем, что пользователь действительно студент
        if ($student->role !== 'student') {
            abort(404); // Или другое подходящее действие
        }

        // Удаление студента
        $student->delete();

        // Перенаправление после успешного удаления
        return redirect()->route('admin.students.index')->with('success', 'Студент успешно удален!');
    }

    /**
     * Отображает главную страницу личного кабинета студента.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function dashboard()
    {
        // Здесь можно получить данные, специфичные для студента
        $student = Auth::user(); // Получаем текущего авторизованного студента

        return view('student.dashboard', compact('student'));
    }
} 