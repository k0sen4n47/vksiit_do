<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Services\HelperService;

class RegisterController extends Controller
{
    public function create()
    {
        // Получаем список всех групп для выбора при регистрации
        $groups = Group::all();
        // Передаем список групп в представление
        return view('auth.register', compact('groups'));
    }
    public function store(Request $request)
    {
        // Валидация данных формы регистрации
        $validator = Validator::make($request->all(), [
            'fio' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'group_id' => 'required|exists:groups,id', // Группа обязательна
            'subgroup' => ['nullable', Rule::in(['first', 'second'])], // Подгруппа необязательна
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $validator->validated();

        // Получаем выбранную группу для генерации логина
        $group = Group::findOrFail($data['group_id']);

        // Определяем порядковый номер студента в группе для логина
        $sequentialNumber = $group->students()->count() + 1;

        // Генерируем логин по формату 'аббревиатура_группы-порядковый_номер'
        $shortNamePart = HelperService::transliterateCyrillicToLatin($group->short_name);
        $login = strtolower($shortNamePart . '-' . $sequentialNumber);

        // Проверка на уникальность логина (запасной вариант)
        $originalLogin = $login;
        $count = 1;
        while (User::where('login', $login)->exists()) {
             $login = $originalLogin . '_' . $count++;
        }

        // Создание нового пользователя с ролью 'student'
        $user = User::create([
            'fio' => $data['fio'],
            'name' => $data['fio'], // Используем ФИО для поля name
            'email' => $data['email'],
            'group_id' => $data['group_id'],
            'subgroup' => $data['subgroup'] ?? null, // Используем выбранную подгруппу или null
            'role' => 'student', // Устанавливаем роль как студент
            'login' => $login, // Сгенерированный логин
            'password' => Hash::make($data['password']),
        ]);

        // Автоматический вход пользователя после регистрации
        Auth::login($user);

        // Перенаправление после успешной регистрации
        return redirect()->route('cabinet');
    }
}
