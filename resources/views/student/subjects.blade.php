@extends('layouts.app')

@section('title-page')
Мои предметы
@endsection

@section('content')
<div class="assignment-subjects">
    <div class="assignment-subjects__header">
        <h1 class="assignment-subjects__title">Мои предметы</h1>
    </div>
    <div class="assignment-subjects__list">
        @if($subjects->isEmpty())
        <div class="assignment-subjects__empty">
            У вас пока нет доступных предметов.
        </div>
        @else
        @foreach($subjects as $subject)
        <div class="assignment-subjects__item">
            <div class="assignment-subjects__item-content">
                <h3>{{ $subject->name }}</h3>
                <div>{{ $subject->description ?? 'Нет описания' }}</div>
            </div>
            <a href="{{ route('student.subjects.show', $subject->id) }}" class="assignment-subjects__item-button btn">Перейти к предмету</a>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection