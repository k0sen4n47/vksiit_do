@extends('layouts.app')

@section('content')
<div class="assignment-groups">
    <div class="assignment-groups__header">
        <h1 class="assignment-groups__title">Выберите группу для предмета "{{ $subject->name }}"</h1>
        <a href="{{ route('teacher.assignments.subjects.index') }}" class="assignment-groups__back">
            Вернуться к выбору предмета
        </a>
    </div>

    @if(session('error'))
    <div class="assignment-groups__error">
        {{ session('error') }}
    </div>
    @endif

    <div class="assignment-groups__list">
        @forelse($groups as $group)
        <div class="assignment-groups__item">
            <div class="assignment-groups__item-content">
                <h2 class="assignment-groups__item-title">{{ $group->short_name }}</h2>
                @if($group->description)
                <div class="assignment-groups__item-description">{{ $group->description }}</div>
                @endif
            </div>
            <div class="assignment-groups__item-actions">
                <a href="{{ route('teacher.assignments.create', ['subjectId' => $subject->id, 'groupId' => $group->id]) }}"
                    class="assignment-groups__item-button btn empty">
                    Создать задание
                </a>
            </div>
        </div>
        @empty
        <div class="assignment-groups__empty">
            У вас нет доступных групп для этого предмета
        </div>
        @endforelse
    </div>
</div>
@endsection