<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Запускает наполнение базы данных.
     */
    public function run(): void
    {
        // Создаем пользователя-администратора, если его нет (проверяем по логину)
        if (!User::where('login', 'admin')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com', // Оставим email, но для входа использовать не будем
                'login' => 'admin', // Добавляем логин
                'password' => Hash::make('admin'), // Хешируем пароль
                'role' => 'admin', // Устанавливаем роль 'admin'
            ]);
        }
    }
}
