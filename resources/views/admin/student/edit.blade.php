@extends('admin.dashboard')

@section('admin_content')
    <h2>Редактировать студента: {{ $student->fio }}</h2> {{-- Заголовок с ФИО студента --}}

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.students.update', $student) }}" method="POST"> {{-- Действие формы отправляется на маршрут обновления --}}
        @csrf
        @method('PUT') {{-- Метод PUT для обновления --}}

        <div class="form-group">
            <label for="fio">ФИО студента:</label>
            <input type="text" name="fio" id="fio" class="form-control" value="{{ old('fio', $student->fio) }}" required> {{-- Значение из модели --}}
            @error('fio')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email студента:</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $student->email) }}" required> {{-- Значение из модели --}}
            @error('email')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="group_id">Группа:</label>
            <select name="group_id" id="group_id" class="form-control" required> {{-- Выбор группы --}}
                <option value="">Выберите группу</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id', $student->group_id) == $group->id ? 'selected' : '' }}>{{ $group->short_name }}</option> {{-- Отображаем только короткое название --}}
                @endforeach
            </select>
            @error('group_id')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Поле для выбора подгруппы --}}
        <div class="form-group"> {{-- Контейнер для поля Подгруппа --}}
            <label for="subgroup">Подгруппа:</label>
            <select name="subgroup" id="subgroup" class="form-control">
                <option value="">Не выбрано</option>
                <option value="first" {{ old('subgroup', $student->subgroup) == 'first' ? 'selected' : '' }}>Первая подгруппа</option>
                <option value="second" {{ old('subgroup', $student->subgroup) == 'second' ? 'selected' : '' }}>Вторая подгруппа</option>
            </select>
            @error('subgroup')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Поля Логин и Пароль не отображаем для редактирования --}}
        {{-- <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" class="form-control" value="{{ $student->login }}" disabled> --}}
        {{-- </div> --}}

        {{-- <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="text" id="password" class="form-control" value="(скрыт)" disabled> --}}
        {{-- </div> --}}

        <button type="submit" class="btn btn-primary">Обновить данные</button>
    </form>
@endsection 