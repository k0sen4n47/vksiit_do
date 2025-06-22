@extends('layouts.app')

@section('content')
<div class="assignment-subjects">
    <div class="assignment-subjects__header">
        <h1 class="assignment-subjects__title">Выберите предмет</h1>
    </div>

    @if(session('error'))
    <div class="assignment-subjects__error">
        {{ session('error') }}
    </div>
    @endif

    <div class="assignment-subjects__list">
        @forelse($subjects as $subject)

        <div class="assignment-subjects__item">
            @if($subject->image)
            <img src="{{ asset('storage/' . $subject->image) }}" alt="{{ $subject->name }}" class="assignment-subjects__item-image">
            @endif
            <div class="assignment-subjects__item-content">
                <h2 class="assignment-subjects__item-title">{{ $subject->name }}</h2>
                @if($subject->abbreviation)
                <div class="assignment-subjects__item-abbr">{{ $subject->abbreviation }}</div>
                @endif
                @if($subject->description)
                <div class="assignment-subjects__item-description p1">{{ $subject->description }}</div>
                @endif
            </div>
            <div class="assignment-subjects__item-actions">
                <a href="{{ route('teacher.assignments.groups.index', ['subjectId' => $subject->id]) }}"
                    class="assignment-subjects__item-button btn">
                    Выбрать предмет
                </a>
            </div>
        </div>
        @empty
        <div class="assignment-subjects__empty п2">
            У вас нет доступных предметов
        </div>
        @endforelse
    </div>
</div>
@endsection