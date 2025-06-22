<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Assignment;
use App\Models\AssignmentStudentAnswer;
use App\Models\User;

try {
    echo "=== ПРОВЕРКА ЗАДАНИЙ И ОТВЕТОВ ===\n\n";
    
    // Проверяем все задания
    echo "1. Все задания:\n";
    $assignments = Assignment::all(['id', 'title', 'status']);
    foreach ($assignments as $assignment) {
        echo "ID: {$assignment->id}, Название: {$assignment->title}, Статус: {$assignment->status}\n";
    }
    
    echo "\n2. Ответы студентов:\n";
    $answers = AssignmentStudentAnswer::with(['student', 'assignmentPage.assignment'])->get();
    foreach ($answers as $answer) {
        echo "Студент: {$answer->student->fio}, Задание: {$answer->assignmentPage->assignment->title}, Статус задания: {$answer->assignmentPage->assignment->status}\n";
    }
    
    echo "\n3. Статистика по статусам:\n";
    $activeCount = Assignment::where('status', 'active')->count();
    $completedCount = Assignment::where('status', 'completed')->count();
    $archivedCount = Assignment::where('status', 'archived')->count();
    
    echo "Активные: {$activeCount}\n";
    echo "Выполненные: {$completedCount}\n";
    echo "Архивные: {$archivedCount}\n";
    
    echo "\n4. Проверяем конкретное задание ID 36:\n";
    $assignment36 = Assignment::find(36);
    if ($assignment36) {
        echo "Задание 36: {$assignment36->title}, Статус: {$assignment36->status}\n";
        
        // Проверяем ответы на это задание
        $answersFor36 = AssignmentStudentAnswer::whereHas('assignmentPage', function($query) {
            $query->where('assignment_id', 36);
        })->with(['student', 'assignmentPage'])->get();
        
        echo "Ответы на задание 36:\n";
        foreach ($answersFor36 as $answer) {
            echo "- Студент: {$answer->student->fio}, Страница: {$answer->assignmentPage->id}, Отправлено: {$answer->submitted_at}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
} 