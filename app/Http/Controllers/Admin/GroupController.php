<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Models\GroupNameComponent;

class GroupController extends Controller
{
    /**
     * Отображает форму создания новой группы.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function createGroup()
    {
        // Получаем список пользователей с ролью 'student'
        $students = User::where('role', 'student')->get();

        // Получаем список пользователей с ролью 'teacher'
        $teachers = User::where('role', 'teacher')->get();

        // Получаем список всех компонентов названий групп
        $nameComponents = GroupNameComponent::all();

        // Возвращаем представление формы создания группы, передавая списки студентов, преподавателей и компоненты названий
        return view('admin.group.create', compact('students', 'teachers', 'nameComponents'));
    }

    /**
     * Сохраняет новую группу в базе данных.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeGroup(Request $request)
    {
        // Валидация данных формы
        $validator = Validator::make($request->all(), [
            'name_component_id' => 'required|exists:group_name_components,id', // ID компонента названия обязателен и должен существовать
            'course' => 'required|integer|min:1|max:4', // Курс обязателен, целое число, минимум 1, максимум 4
            'year' => 'required|integer|min:0|max:99', // Год поступления обязателен, целое число 0-99
            'suffix' => 'nullable|string|max:255', // Суффикс необязателен
            'curator_id' => 'nullable|exists:users,id', // Куратор опционален, должен существовать в таблице users
            'students' => 'nullable|array', // Студенты опционально, должны быть массивом
            'students.*' => 'exists:users,id', // Каждый элемент массива students должен существовать в таблице users
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = $validator->validated();

        // Получаем компонент названия группы
        $nameComponent = GroupNameComponent::findOrFail($data['name_component_id']);

        // Формируем полное и сокращенное название группы
        $fullGroupName = $nameComponent->full_name . ' ' . $data['course'] . ' курс ' . $data['year'] . ' года' . ($data['suffix'] ? ' (' . $data['suffix'] . ')' : '');
        $shortGroupName = $nameComponent->short_name . '-' . $data['course'] . $data['year'] . ($data['suffix'] ? '-' . $data['suffix'] : '');

        // Дополнительная валидация на уникальность сформированного названия и сокращенного названия
        $nameValidator = Validator::make([
            'name' => $fullGroupName,
            'short_name' => $shortGroupName,
        ], [
            'name' => 'unique:groups,name',
            'short_name' => 'unique:groups,short_name',
        ]);

        if ($nameValidator->fails()) {
            return redirect()->back()
                        ->withErrors($nameValidator)
                        ->withInput($request->all());
        }

        try {
            // Создание новой группы
            $group = Group::create([
                'name' => $fullGroupName,
                'short_name' => $shortGroupName,
                'course' => $data['course'],
                'year' => $data['year'],
                'suffix' => $data['suffix'] ?? null,
                'curator_id' => $data['curator_id'] ?? null,
            ]);

            // Привязка студентов к группе
            if (!empty($data['students'])) {
                User::whereIn('id', $data['students'])->update(['group_id' => $group->id]);
            }

            return redirect()->route('admin.groups.index')->with('success', 'Группа успешно создана!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Ошибка при создании группы: ' . $e->getMessage()])
                ->withInput($request->all());
        }
    }

    /**
     * Отображает список всех групп с возможностью фильтрации.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function indexGroups(Request $request)
    {
        // Начинаем запрос к группам с загрузкой куратора
        $query = Group::with(['curator', 'nameComponent']);

        // Фильтр по поиску по буквам (по названию или сокращенному названию)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('short_name', 'like', '%' . $search . '%');
            });
        }

        // Фильтр по курсу
        if ($request->filled('course')) {
            $query->where('course', $request->input('course'));
        }

        // Фильтр по наличию/отсутствию суффикса
        if ($request->filled('suffix_status')) {
            if ($request->input('suffix_status') == 'with_suffix') {
                $query->whereNotNull('suffix');
            } elseif ($request->input('suffix_status') == 'without_suffix') {
                $query->whereNull('suffix');
            }
        }

        // Получаем отфильтрованные группы
        $groups = $query->get();

        // Передаем в представление данные групп и текущие параметры фильтрации
        return view('admin.group.index', ['groups' => $groups, 'request' => $request]);
    }

    /**
     * Отображает форму редактирования группы.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Contracts\View\View
     */
    public function editGroup(Group $group)
    {
        // Получаем список пользователей с ролью 'student'
        $students = User::where('role', 'student')->get();

        // Получаем список пользователей с ролью 'teacher'
        $teachers = User::where('role', 'teacher')->get();

        // Возвращаем представление формы редактирования, передавая данные группы, студентов и преподавателей
        return view('admin.group.edit', compact('group', 'students', 'teachers'));
    }

    /**
     * Обновляет данные группы в базе данных.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGroup(Request $request, Group $group)
    {
        // Валидация данных формы
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'unique:groups,name,' . $group->id], // Полное название обязательно, уникально игнорируя текущую группу
            'short_name' => 'required|string|max:255', // Сокращенное название обязательно
            'course' => 'required|integer|min:1|max:4', // Курс обязателен, целое число, минимум 1, максимум 4
            'year' => 'required|integer|min:0|max:99', // Год поступления обязателен, целое число 0-99
            'suffix' => 'nullable|string|max:255', // Суффикс необязателен
            'curator_id' => 'nullable|exists:users,id', // Куратор опционален, должен существовать в таблице users
            'students' => 'nullable|array', // Студенты опционально, должны быть массивом
            'students.*' => 'exists:users,id', // Каждый элемент массива students должен существовать в таблице users
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Обновление данных группы
        $group->update([
            'name' => $data['name'],
            'short_name' => $data['short_name'],
            'course' => $data['course'],
            'year' => $data['year'],
            'suffix' => $data['suffix'] ?? null,
            'curator_id' => $data['curator_id'],
        ]);

        // Обновление студентов в группе
        // Получаем текущих студентов группы
        $currentStudents = User::where('group_id', $group->id)->pluck('id')->toArray();

        // Определяем студентов, которых нужно добавить и удалить
        $studentsToAdd = array_diff($data['students'] ?? [], $currentStudents);
        $studentsToRemove = array_diff($currentStudents, $data['students'] ?? []);

        // Удаляем студентов, которых нужно удалить
        if (!empty($studentsToRemove)) {
            User::whereIn('id', $studentsToRemove)->update(['group_id' => null]);
        }

        // Добавляем студентов, которых нужно добавить
        if (!empty($studentsToAdd)) {
            User::whereIn('id', $studentsToAdd)->update(['group_id' => $group->id]);
        }

        // Перенаправление после успешного обновления
        return redirect()->route('admin.groups.index')->with('success', 'Группа успешно обновлена!');
    }

    /**
     * Удаляет группу из базы данных.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyGroup(Group $group)
    {
        // Перед удалением группы, отвязываем всех связанных студентов
        User::where('group_id', $group->id)->update(['group_id' => null]);

        // Удаление группы
        $group->delete();

        // Перенаправление после успешного удаления
        return redirect()->route('admin.groups.index')->with('success', 'Группа успешно удалена!');
    }
} 