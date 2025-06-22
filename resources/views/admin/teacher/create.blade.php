@extends('admin.dashboard')

@section('admin_content')
<div class="dashboard__create-content">
    <h3>Создать нового преподавателя</h3>

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

    <form action="{{ route('admin.teachers.store') }}" method="POST">
        @csrf
        <div class="form-create">
            <div class="form-create__top">
                <div class="form-group">
                    <label for="fio">ФИО преподавателя:</label>
                    <input type="text" name="fio" id="fio" class="form-control" value="{{ old('fio') }}" required>
                    @error('fio')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email преподавателя:</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-create__bot">
                <button type="submit" class="btn btn-primary">Создать преподавателя</button>
            </div>
        </div>
    </form>
</div>
@endsection