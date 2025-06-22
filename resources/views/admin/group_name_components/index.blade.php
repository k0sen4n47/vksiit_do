@extends('admin.dashboard')

@section('admin_content')
<div class="dashboard__create-content">
    <h2>Список компонентов названий групп</h2>

    @if ($components->isEmpty())
    <p>Компоненты названий групп пока не созданы.</p>
    @else
    <div class="group-name-component-list admin-list">
        @foreach ($components as $component)
        <div class="group-name-component-list__item admin-list__item">
            <div class="group-name-component-list__info admin-list__info">
                <div class="group-name-component-list__full-name admin-list__name">{{ $component->full_name }}</div>
                <div class="group-name-component-list__short-name admin-list__short-name ">{{ $component->short_name }}</div>
            </div>
            <div class="group-name-component-list__actions admin-list__actions">
                <a href="{{ route('admin.group-name-components.edit', $component) }}" class="btn editor">Редактировать</a>
                <form action="{{ route('admin.group-name-components.destroy', $component) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn deletor" onclick="return confirm('Вы уверены, что хотите удалить этот компонент?');">Удалить</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection