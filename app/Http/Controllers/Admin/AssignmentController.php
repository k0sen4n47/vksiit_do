<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Subject;
use App\Models\Group;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    /**
     * Отображает форму создания нового задания.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $subjects = Subject::all();
        $groups = Group::all();
        return view('admin.assignment.create', compact('subjects', 'groups'));
    }

    /**
     * Сохраняет новое задание в базе данных.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:now',
            'max_score' => 'required|integer|min:1|max:100',
            'files.*' => 'nullable|file|max:10240', // Максимальный размер файла 10MB
            'groups' => 'required|array|min:1',
            'groups.*' => 'exists:groups,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Создаем задание
            $assignment = Assignment::create([
                'subject_id' => $request->subject_id,
                'title' => $request->title,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'max_score' => $request->max_score,
            ]);

            // Сохраняем файлы
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('assignments/' . $assignment->id);
                    $assignment->files()->create([
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            }

            // Связываем задание с группами
            $assignment->groups()->attach($request->groups);

            return redirect()->route('admin.assignments.index')
                ->with('success', 'Задание успешно создано!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Произошла ошибка при создании задания: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Отображает список всех заданий.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $assignments = Assignment::with(['subject', 'groups'])->get();
        return view('admin.assignment.index', compact('assignments'));
    }

    /**
     * Отображает форму редактирования задания.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Assignment $assignment)
    {
        $subjects = Subject::all();
        $groups = Group::all();
        return view('admin.assignment.edit', compact('assignment', 'subjects', 'groups'));
    }

    /**
     * Обновляет задание в базе данных.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Assignment $assignment)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:now',
            'max_score' => 'required|integer|min:1|max:100',
            'files.*' => 'nullable|file|max:10240',
            'groups' => 'required|array|min:1',
            'groups.*' => 'exists:groups,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Обновляем основные данные задания
            $assignment->update([
                'subject_id' => $request->subject_id,
                'title' => $request->title,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'max_score' => $request->max_score,
            ]);

            // Обрабатываем новые файлы
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('assignments/' . $assignment->id);
                    $assignment->files()->create([
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                    ]);
                }
            }

            // Обновляем связи с группами
            $assignment->groups()->sync($request->groups);

            return redirect()->route('admin.assignments.index')
                ->with('success', 'Задание успешно обновлено!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Произошла ошибка при обновлении задания: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Удаляет задание из базы данных.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Assignment $assignment)
    {
        try {
            // Удаляем все файлы задания
            foreach ($assignment->files as $file) {
                Storage::delete($file->path);
                $file->delete();
            }

            // Удаляем само задание
            $assignment->delete();

            return redirect()->route('admin.assignments.index')
                ->with('success', 'Задание успешно удалено!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Произошла ошибка при удалении задания: ' . $e->getMessage()]);
        }
    }
} 