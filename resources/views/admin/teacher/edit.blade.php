@extends('admin.dashboard')

@section('admin_content')
    <h2>Редактировать преподавателя: {{ $teacher->fio }}</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST">
        @csrf
        @method('PUT') {{-- Используем метод PUT для обновления --}}

        <div class="form-group">
            <label for="fio">ФИО:</label>
            <input type="text" name="fio" id="fio" class="form-control" value="{{ old('fio', $teacher->fio) }}" required>
            @error('fio')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $teacher->email) }}" required>
            @error('email')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Логин и пароль не редактируются через эту форму --}}

        <button type="submit" class="btn btn-primary">Обновить преподавателя</button>
    </form>
@endsection 