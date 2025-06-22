<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubjectController extends Controller
{
    /**
     * Отображает форму создания нового предмета.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function createSubject()
    {
        // Получаем список преподавателей
        $teachers = User::where('role', 'teacher')->get();

        // Получаем список групп
        $groups = Group::all();

        // Передаем списки в представление
        return view('admin.subject.create', compact('teachers', 'groups'));
    }

    /**
     * Сохраняет новый предмет в базе данных.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSubject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:subjects',
            'abbreviation' => 'required|string|max:255',
            'connections.*.teacher_id' => 'required|exists:users,id',
            'connections.*.group_ids' => 'required|array',
            'connections.*.group_ids.*' => 'exists:groups,id',
            'image' => 'nullable|file|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            // Обработка загрузки изображения
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = strtolower($file->getClientOriginalExtension());
                $allowedExtensions = ['jpeg', 'png', 'jpg', 'gif'];
                
                if (!in_array($extension, $allowedExtensions)) {
                    throw new \Exception('Недопустимый формат файла. Разрешены только jpeg, png, jpg, gif.');
                }
                $imagePath = $file->store('subjects', 'public');
                $data['image'] = $imagePath;
            }

            // Создаем предмет
            $subject = Subject::create([
                'name' => $data['name'],
                'abbreviation' => $data['abbreviation'],
                'image' => $data['image'] ?? null
            ]);

            // Сохраняем связи предмет-преподаватель-группа
            if (isset($data['connections'])) {
                foreach ($data['connections'] as $connection) {
                    if (!empty($connection['teacher_id']) && !empty($connection['group_ids'])) {
                        foreach ($connection['group_ids'] as $group_id) {
                            DB::table('subject_teacher_group')->insert([
                                'subject_id' => $subject->id,
                                'user_id' => $connection['teacher_id'],
                                'group_id' => $group_id
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.subjects.index')->with('success', 'Предмет успешно создан!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Произошла ошибка при создании предмета: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Отображает список всех предметов с возможностью фильтрации.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function indexSubjects(Request $request)
    {
        $query = Subject::query();

        // Фильтр по поиску по названию предмета
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', '%' . $search . '%');
        }

        $subjects = $query->get();

        return view('admin.subject.index', ['subjects' => $subjects, 'request' => $request]);
    }

    /**
     * Отображает форму редактирования предмета.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Contracts\View\View
     */
    public function editSubject(Subject $subject)
    {
        // Получаем список преподавателей
        $teachers = User::where('role', 'teacher')->get();

        // Получаем список групп
        $groups = Group::all();

        // Получаем текущие связи предмета
        $subject->load('teacherGroupConnections');

        return view('admin.subject.edit', compact('subject', 'teachers', 'groups'));
    }

    /**
     * Обновляет данные предмета в базе данных.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSubject(Request $request, Subject $subject)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'abbreviation' => 'required|string|max:255',
            'connections.*.teacher_id' => 'required|exists:users,id',
            'connections.*.group_ids' => 'required|array',
            'connections.*.group_ids.*' => 'exists:groups,id',
            'image' => 'nullable|file|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $validator->validated();

        try {
            DB::beginTransaction();

            // Обработка загрузки изображения
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $extension = strtolower($file->getClientOriginalExtension());
                $allowedExtensions = ['jpeg', 'png', 'jpg', 'gif'];
                
                if (!in_array($extension, $allowedExtensions)) {
                    throw new \Exception('Недопустимый формат файла. Разрешены только jpeg, png, jpg, gif.');
                }
                // Удаляем старое изображение, если оно существует
                if ($subject->image) {
                    Storage::disk('public')->delete($subject->image);
                }
                $imagePath = $file->store('subjects', 'public');
                $data['image'] = $imagePath;
            }

            // Обновляем основные данные предмета
            $subject->update([
                'name' => $data['name'],
                'abbreviation' => $data['abbreviation'],
                'image' => $data['image'] ?? $subject->image
            ]);

            // Обрабатываем связи
            if (isset($data['connections'])) {
                // Удаляем все существующие связи
                DB::table('subject_teacher_group')
                    ->where('subject_id', $subject->id)
                    ->delete();

                // Добавляем новые связи
                foreach ($data['connections'] as $connection) {
                    if (!empty($connection['teacher_id']) && !empty($connection['group_ids'])) {
                        foreach ($connection['group_ids'] as $group_id) {
                            DB::table('subject_teacher_group')->insert([
                                'subject_id' => $subject->id,
                                'user_id' => $connection['teacher_id'],
                                'group_id' => $group_id
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.subjects.index')->with('success', 'Предмет успешно обновлен!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Произошла ошибка при обновлении предмета: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Удаляет предмет из базы данных.
     *
     * @param  \App\Models\Subject  $subject
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroySubject(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Предмет успешно удален!');
    }
} 