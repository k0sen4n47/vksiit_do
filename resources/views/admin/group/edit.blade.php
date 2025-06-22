@extends('admin.dashboard')

@section('admin_content')
<div class="dashboard__create-content"> {{-- Основной контейнер формы --}}
    <h2 class="title">Редактировать группу: {{ $group->name }}</h2> {{-- Заголовок формы с названием группы --}}

    <form action="{{ route('admin.groups.update', $group) }}" method="POST"> {{-- Действие формы отправляется на маршрут обновления --}}
        @csrf
        @method('PUT') {{-- Метод PUT для обновления --}}
        <div class="form-create">
            <div class="form-create__top"> {{-- Контейнер для верхних элементов формы --}}
                <div class="form-group"> {{-- Контейнер для поля Полное название группы --}}
                    <label class="label-input" for="name">Полное название группы:</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $group->name) }}" required> {{-- Значение из модели --}}
                </div>

                <div class="form-group"> {{-- Контейнер для поля Сокращенное название группы --}}
                    <label class="label-input" for="short_name">Сокращенное название группы:</label>
                    <input type="text" name="short_name" id="short_name" class="form-control" value="{{ old('short_name', $group->short_name) }}" required> {{-- Значение из модели --}}
                </div>

                <div class="form-group"> {{-- Контейнер для поля Курс --}}
                    <label class="label-input" for="course">Курс:</label>
                    <input type="number" name="course" id="course" class="form-control" value="{{ old('course', $group->course) }}" required min="1"> {{-- Значение из модели --}}
                </div>

                <div class="form-group"> {{-- Контейнер для поля Год поступления --}}
                    <label class="label-input" for="year">Год поступления (две последние цифры):</label>
                    <input type="number" name="year" id="year" class="form-control" value="{{ old('year', $group->year) }}" required min="0" max="99"> {{-- Значение из модели --}}
                </div>

                <div class="form-group"> {{-- Контейнер для поля Суффикс --}}
                    <label class="label-input" for="suffix">Суффикс:</label>
                    <input type="text" name="suffix" id="suffix" class="form-control" value="{{ old('suffix', $group->suffix) }}" required> {{-- Значение из модели --}}
                </div>

                <div class="form-group"> {{-- Контейнер для поля Куратор группы --}}
                    <label class="label-input" for="curator_id">Куратор группы:</label>
                    <select name="curator_id" id="curator_id" class="form-control"> {{-- Куратор обязателен --}}
                        <option value="">Выберите куратора</option>
                        @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('curator_id', $group->curator_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option> {{-- Выбираем текущего куратора --}}
                        @endforeach
                    </select>
                </div>

                <!-- <div class="form-group"> {{-- Контейнер для поля Студенты группы --}}
                    <label class="label-input" for="students">Студенты группы:</label>
                    {{-- Используем select с multiple для выбора нескольких студентов --}}
                    <select name="students[]" id="students" class="form-control" multiple>
                        @foreach ($students as $student)
                        <option value="{{ $student->id }}" {{ in_array($student->id, old('students', $group->students->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $student->name }}</option> {{-- Выбираем студентов, которые уже в группе --}}
                        @endforeach
                    </select>
                </div> -->
            </div>
            <button type="submit" class="btn btn-primary">Обновить группу</button> {{-- Кнопка отправки --}}
        </div>
    </form>
</div>
@endsection