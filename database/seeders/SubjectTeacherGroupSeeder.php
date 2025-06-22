<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Subject;
use App\Models\Group;

class SubjectTeacherGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем тестового преподавателя
        $teacher = User::create([
            'name' => 'Test Teacher',
            'email' => 'teacher@example.com',
            'login' => 'teacher',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // Создаем тестовые предметы
        $subjects = [
            Subject::create([
                'name' => 'Математика',
                'abbreviation' => 'МАТ'
            ]),
            Subject::create([
                'name' => 'Физика',
                'abbreviation' => 'ФИЗ'
            ]),
            Subject::create([
                'name' => 'Информатика',
                'abbreviation' => 'ИНФ'
            ]),
        ];

        // Создаем тестовые группы
        $groups = [
            Group::create([
                'name' => 'Группа 1',
                'short_name' => 'Г1',
                'course' => 1,
                'year' => 2024,
                'suffix' => 'А',
                'curator_id' => $teacher->id
            ]),
            Group::create([
                'name' => 'Группа 2',
                'short_name' => 'Г2',
                'course' => 1,
                'year' => 2024,
                'suffix' => 'Б',
                'curator_id' => $teacher->id
            ]),
            Group::create([
                'name' => 'Группа 3',
                'short_name' => 'Г3',
                'course' => 2,
                'year' => 2024,
                'suffix' => 'А',
                'curator_id' => $teacher->id
            ]),
        ];

        // Создаем связи между преподавателем, предметами и группами
        foreach ($subjects as $subject) {
            foreach ($groups as $group) {
                DB::table('subject_teacher_group')->insert([
                    'user_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'group_id' => $group->id
                ]);
            }
        }
    }
} 