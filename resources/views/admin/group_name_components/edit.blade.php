@extends('admin.dashboard')

@section('admin_content')
<div class="dashboard__create-content">
    <h2>Редактировать компонент названия группы: {{ $groupNameComponent->full_name }}</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.group-name-components.update', $groupNameComponent) }}" method="POST">
        @csrf
        @method('PUT') {{-- Используем метод PUT для обновления --}}
        <div class="form-create">
            <div class="form-create__top">
                <div class="form-group">
                    <label for="full_name">Полное название:</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="{{ old('full_name', $groupNameComponent->full_name) }}" required>
                </div>

                <div class="form-group">
                    <label for="short_name">Сокращенное название:</label>
                    <input type="text" name="short_name" id="short_name" class="form-control" value="{{ old('short_name', $groupNameComponent->short_name) }}" required>
                </div>
            </div>
            <div class="form-create__bot">
                <button type="submit" class="btn btn-primary">Обновить</button>
                <a href="{{ route('admin.group-name-components.index') }}" class="btn btn-secondary">Отмена</a>
            </div>
        </div>
    </form>
</div>
@endsection