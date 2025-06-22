<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Запускает наполнение базы данных приложения.
     */
    public function run(): void
    {
        // Вызываем сидер для создания администратора
        $this->call(AdminUserSeeder::class);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            SubjectTeacherGroupSeeder::class,
        ]);
    }
}
