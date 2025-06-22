<?php

namespace App\Http\Controllers\Teacher\Assignment;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentFile;
use App\Models\Group;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\App\Http\Middleware\RoleMiddleware::class.':teacher');
    }

    public function edit(Assignment $assignment)
    {
        $teacher = Auth::user();
        
        // Проверяем доступ к заданию
        if ($assignment->teacher_id !== $teacher->id) {
            return redirect()->back()->with('error', 'У вас нет доступа к этому заданию');
        }

        $subjects = $teacher->subjects;
        $groups = $teacher->groups;

        return view('teacher.assignment.edit.index', compact('assignment', 'subjects', 'groups'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        try {
            Log::info('Starting assignment update process', [
                'teacher_id' => auth()->id(),
                'assignment_id' => $assignment->id,
                'request_data' => $request->all()
            ]);

            // Проверяем доступ к заданию
            $teacher = auth()->user();
            if ($assignment->teacher_id !== $teacher->id) {
                Log::error('Teacher does not have access to the assignment', [
                    'teacher_id' => $teacher->id,
                    'assignment_id' => $assignment->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к этому заданию'
                ], 403);
            }

            // Валидация основных полей
            $validated = $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'group_id' => 'required|exists:groups,id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'deadline' => 'required|date|after:now',
                'files.*' => 'nullable|file|mimes:pdf,doc,docx,zip,rar|max:10240'
            ]);

            // Проверяем доступ учителя к предмету и группе
            $subject = Subject::findOrFail($validated['subject_id']);
            $group = Group::findOrFail($validated['group_id']);

            if (!$teacher->subjects->contains($subject)) {
                Log::error('Teacher does not have access to the subject', [
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к выбранному предмету'
                ], 403);
            }

            if (!$teacher->groups->contains($group)) {
                Log::error('Teacher does not have access to the group', [
                    'teacher_id' => $teacher->id,
                    'group_id' => $group->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет доступа к выбранной группе'
                ], 403);
            }

            // Обновляем задание
            $assignment->update([
                'subject_id' => $validated['subject_id'],
                'group_id' => $validated['group_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'deadline' => $validated['deadline']
            ]);

            // Удаляем старые страницы
            $assignment->pages()->delete();

            // Сохраняем новые страницы
            if ($request->has('pages')) {
                foreach ($request->input('pages') as $pageData) {
                    $content = [];
                    if ($pageData['type'] === 'code') {
                        $content = [
                            'html' => $pageData['content']['html'] ?? '',
                            'css' => $pageData['content']['css'] ?? '',
                        ];
                    } elseif ($pageData['type'] === 'text') {
                        $content = [
                            'text' => $pageData['content']['text'] ?? '',
                        ];
                    }
                    $assignment->pages()->create([
                        'type' => $pageData['type'],
                        'content' => $content,
                        'order' => $pageData['order'] ?? 0,
                        'title' => $pageData['title'] ?? '',
                    ]);
                }
            }

            // Обрабатываем файлы
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('assignments/' . $assignment->id, 'public');
                    AssignmentFile::create([
                        'assignment_id' => $assignment->id,
                        'file_path' => $path,
                        'original_name' => $file->getClientOriginalName()
                    ]);
                }
            }

            Log::info('Assignment updated successfully', [
                'assignment_id' => $assignment->id,
                'teacher_id' => $teacher->id
            ]);

            return redirect()->route('teacher.assignments.show', $assignment)
                ->with('success', 'Задание успешно обновлено');

        } catch (\Exception $e) {
            Log::error('Error updating assignment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Произошла ошибка при обновлении задания: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateStatus(Request $request, Assignment $assignment)
    {
        $request->validate([
            'status' => 'required|in:active,completed,archived'
        ]);
        $assignment->status = $request->status;
        $assignment->save();
        return response()->json(['success' => true]);
    }
} 