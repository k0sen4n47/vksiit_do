@extends('admin.dashboard')


@section('admin_content')
<div class="dashboard__create-content">
    <h3>Создать нового студента</h3>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
        @if (session('generated_login'))
        <p><strong>Сгенерированный логин:</strong> {{ session('generated_login') }}</p>
        @endif
        @if (session('generated_password'))
        <p><strong>Сгенерированный пароль:</strong> {{ session('generated_password') }}</p>
        @endif
    </div>
    @endif

    <form action="{{ route('admin.students.store') }}" method="POST"> {{-- Действие формы будет на маршрут сохранения студента --}}
        @csrf
        <div class="form-create">
            <div class="form-create__top">
                <div class="form-group">
                    <label for="fio">ФИО студента:</label>
                    <input type="text" name="fio" id="fio" class="form-control" value="{{ old('fio') }}" required> {{-- Поле для ФИО --}}
                    @error('fio')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email студента:</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required> {{-- Поле для Email --}}
                    @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="group_id">Группа:</label>
                    <select name="group_id" id="group_id" class="form-control" required> {{-- Выбор группы --}}
                        <option value="">Выберите группу</option>
                        @foreach ($groups as $group)
                        <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->short_name }}</option> {{-- Отображаем только короткое название --}}
                        @endforeach
                    </select>
                    @error('group_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-create__bot">
                <button type="submit" class="btn btn-primary">Создать студента</button>
            </div>
        </div>
    </form>
</div>
@endsection