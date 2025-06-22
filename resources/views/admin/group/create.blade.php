@extends('admin.dashboard')

@section('admin_content')
<div class="dashboard__create-content">
    <h3>Создать новую группу</h3>

    <form action="{{ route('admin.groups.store') }}" method="POST"> {{-- Действие формы отправляется на маршрут сохранения --}}
        @csrf
        <div class="form-create">
            <div class="form-create__top">


                <div class="form-group">
                    <label for="name_component_id">Название группы:</label>
                    <select name="name_component_id" id="name_component_id" class="form-control" required>
                        <option value="">Выберите компонент названия</option>
                        @foreach ($nameComponents as $component)
                        <option value="{{ $component->id }}" {{ old('name_component_id') == $component->id ? 'selected' : '' }}>
                            {{ $component->full_name }} ({{ $component->short_name }}{{ $component->suffix ? ' ' . $component->suffix : '' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="course">Курс:</label>
                    <input type="number" name="course" id="course" class="form-control" required min="1">
                </div>

                <div class="form-group">
                    <label for="year">Год поступления (две последние цифры):</label>
                    <input type="number" name="year" id="year" class="form-control" required min="0" max="99">
                </div>

                <div class="form-group">
                    <label for="suffix">Суффикс (необязательно):</label>
                    <input type="text" name="suffix" id="suffix" class="form-control" value="{{ old('suffix') }}">
                </div>

                <div class="form-group">
                    <label for="curator_id">Куратор группы:</label>
                    <select name="curator_id" id="curator_id" class="form-control"> {{-- Сделали необязательным --}}
                        <option value="">Выберите куратора</option>
                        @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('curator_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- <div class="form-group">
                    <label for="students">Студенты группы:</label>
                    {{-- Используем select с multiple для выбора нескольких студентов --}}
                    <select name="students[]" id="students" class="form-control" multiple>
                        @foreach ($students as $student)
                        <option value="{{ $student->id }}" {{ in_array($student->id, old('students', [])) ? 'selected' : '' }}>{{ $student->name }}</option> {{-- Проверяем old('students') как массив --}}
                        @endforeach
                    </select>
                </div> -->
            </div>
            <button type="submit" class="btn btn-primary">Создать группу</button>
        </div>
    </form>
</div>
@endsection