<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentPage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignmentService
{
    /**
     * Создает новое задание с указанными страницами
     *
     * @param array $data
     * @param array $pages
     * @return Assignment
     */
    public function createAssignment(array $data, array $pages): Assignment
    {
        try {
            DB::beginTransaction();

            // Создаем задание
            $assignment = Assignment::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'subject_id' => $data['subject_id'],
                'group_id' => $data['group_id'],
                'teacher_id' => $data['teacher_id'],
                'deadline' => $data['deadline'],
                'status' => 'active',
                'is_active' => true
            ]);

            // Создаем страницы
            foreach ($pages as $index => $page) {
                AssignmentPage::create([
                    'assignment_id' => $assignment->id,
                    'page_number' => $index + 1,
                    'type' => $page['type'],
                    'content' => $page['content']
                ]);
            }

            DB::commit();
            return $assignment;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating assignment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Обновляет существующее задание
     *
     * @param Assignment $assignment
     * @param array $data
     * @param array $pages
     * @return Assignment
     */
    public function updateAssignment(Assignment $assignment, array $data, array $pages): Assignment
    {
        try {
            DB::beginTransaction();

            // Обновляем задание
            $assignment->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'deadline' => $data['deadline']
            ]);

            // Удаляем старые страницы
            $assignment->pages()->delete();

            // Создаем новые страницы
            foreach ($pages as $index => $page) {
                AssignmentPage::create([
                    'assignment_id' => $assignment->id,
                    'page_number' => $index + 1,
                    'type' => $page['type'],
                    'content' => $page['content']
                ]);
            }

            DB::commit();
            return $assignment;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating assignment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Удаляет задание
     *
     * @param Assignment $assignment
     * @return bool
     */
    public function deleteAssignment(Assignment $assignment): bool
    {
        try {
            DB::beginTransaction();

            // Удаляем все связанные данные
            $assignment->pages()->delete();
            $assignment->files()->delete();
            $assignment->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting assignment: ' . $e->getMessage());
            throw $e;
        }
    }
} 