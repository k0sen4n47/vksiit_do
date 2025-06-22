@extends('layouts.app')

@section('title-page')
{{ $subject->name }}
@endsection

@section('content')
<div class="assignment-subjects assignment-subjects--single">
    <div class="assignment-subjects__header">
        <h1 class="assignment-subjects__title">{{ $subject->name }}</h1>
        <div class="assignment-subjects__description">{{ $subject->description ?? 'Нет описания' }}</div>
    </div>
    <div class="assignment-subjects__tabs">
        <a href="{{ route('student.subjects.show', $subject->id) }}?filter=active" 
           class="btn-cabinet {{ $filter === 'active' ? 'btn-cabinet--active' : '' }}">
            Активные
        </a>
        <a href="{{ route('student.subjects.show', $subject->id) }}?filter=completed" 
           class="btn-cabinet {{ $filter === 'completed' ? 'btn-cabinet--active' : '' }}">
            Выполненные
        </a>
        <a href="{{ route('student.subjects.show', $subject->id) }}?filter=archived" 
           class="btn-cabinet {{ $filter === 'archived' ? 'btn-cabinet--active' : '' }}">
            Архивные
        </a>
    </div>
    <div class="assignment-subjects__tab-content">
        @if($assignments->isEmpty())
            <div class="assignment-subjects__empty">
                @if($filter === 'active')
                    Активных заданий по этому предмету пока нет.
                @elseif($filter === 'completed')
                    Выполненных заданий по этому предмету пока нет.
                @elseif($filter === 'archived')
                    Архивных заданий по этому предмету пока нет.
                @else
                    Заданий по этому предмету пока нет.
                @endif
            </div>
        @else
            @foreach($assignments as $assignment)
                <div class="assignment-subjects__item">
                    <div class="assignment-subjects__item-content">
                        <h3>{{ $assignment->title }}</h3>
                        <div>{{ $assignment->description ?? 'Нет описания' }}</div>
                        <div>Срок сдачи: {{ optional($assignment->deadline)->format('d.m.Y H:i') }}</div>
                        <div class="assignment-status">
                            @if($assignment->status === 'active')
                                <span class="status-badge status-badge--active">Активное</span>
                            @elseif($assignment->status === 'completed')
                                <span class="status-badge status-badge--completed">Выполнено</span>
                            @elseif($assignment->status === 'archived')
                                <span class="status-badge status-badge--archived">Архивное</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('student.assignments.show', $assignment->id) }}" class="btn-cabinet">Открыть задание</a>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection 