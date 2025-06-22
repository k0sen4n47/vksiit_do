<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Assignment;
use App\Models\AssignmentStudentAnswer;

try {
    echo "=== ИСПРАВЛЕНИЕ СТАТУСА ЗАДАНИЯ ===\n\n";
    
    // Находим задание с ответами
    $assignment = Assignment::find(36);
    if (!$assignment) {
        echo "Задание не найдено!\n";
        exit;
    }
    
    echo "Текущий статус задания: {$assignment->status}\n";
    
    // Проверяем, есть ли ответы на это задание
    $hasAnswers = AssignmentStudentAnswer::whereHas('assignmentPage', function($query) use ($assignment) {
        $query->where('assignment_id', $assignment->id);
    })->exists();
    
    echo "Есть ли ответы на задание: " . ($hasAnswers ? 'ДА' : 'НЕТ') . "\n";
    
    // Если есть ответы, но статус не completed, исправляем
    if ($hasAnswers && $assignment->status !== 'completed') {
        echo "Исправляем статус задания...\n";
        $assignment->markAsCompleted();
        echo "Статус изменен на: {$assignment->fresh()->status}\n";
    } else {
        echo "Статус уже правильный или нет ответов\n";
    }
    
    // Проверяем все задания после исправления
    echo "\n=== ПРОВЕРКА ПОСЛЕ ИСПРАВЛЕНИЯ ===\n";
    $assignments = Assignment::all(['id', 'title', 'status']);
    foreach ($assignments as $assignment) {
        echo "ID: {$assignment->id}, Название: {$assignment->title}, Статус: {$assignment->status}\n";
    }
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
} 