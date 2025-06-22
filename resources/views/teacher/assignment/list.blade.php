@extends('layouts.app')

@section('title', 'Мои задания')

@section('content')
<div class="assignments-container">
    <h1>Мои задания</h1>
    <!-- <a href="{{ route('teacher.assignments.subjects.index') }}" class="btn">Добавить задание</a> -->

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
    @endif

    @if($assignments->count() > 0)
    @foreach($assignments as $assignment)
    <div class="assignment-card">
        <h3 class="assignment-title">{{ $assignment->title }}</h3>
        <div class="assignment-date">Создано: {{ $assignment->created_at->format('d.m.Y') }}</div>
        @if($assignment->deadline)
        <div class="assignment-date">Срок сдачи: {{ $assignment->deadline->format('d.m.Y H:i') }}</div>
        @endif
        <div class="assignment-description">{{ Str::limit($assignment->description, 150) }}</div>
        <div class="assignment-meta">
            <span>{{ $assignment->subject->name }}</span>
            <span>{{ $assignment->primaryGroup->name }}</span>
            <span>{{ $assignment->pages->count() }} страниц</span>
        </div>
        <div class="assignment-actions">
            <a href="{{ route('teacher.assignments.show', ['assignment' => $assignment->id]) }}">Просмотр</a>
            <a href="{{ route('teacher.assignments.edit', ['assignment' => $assignment->id]) }}">Редактировать</a>
            <form action="{{ route('teacher.assignments.destroy', $assignment) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Вы уверены?')">Удалить</button>
            </form>
            <select class="form-select assignment-status-select" data-assignment-id="{{ $assignment->id }}">
                <option value="active" {{ $assignment->status == 'active' ? 'selected' : '' }}>Активное</option>
                <option value="completed" {{ $assignment->status == 'completed' ? 'selected' : '' }}>Завершено</option>
                <option value="archived" {{ $assignment->status == 'archived' ? 'selected' : '' }}>В архиве</option>
            </select>
        </div>
    </div>
    @endforeach
    @else
    <div class="empty-state">
        <h3>Нет заданий</h3>
        <p>Начните с создания вашего первого задания.</p>
        <a href="{{ route('teacher.assignments.subjects.index') }}" class="btn">Создать задание</a>
    </div>
    @endif

    <!-- @if($teacherSubjects->count() > 0)
    <h2>Быстрое создание</h2>
    @foreach($teacherSubjects as $subject)
    @if($subject->groups->count() > 0)
    <div class="subject-name">{{ $subject->name }}</div>
    <div class="groups">
        @foreach($subject->groups as $group)
        <span>{{ $group->name }}</span>
        @endforeach
    </div>
    @endif
    @endforeach
    @endif -->
</div>
@endsection

@push('scripts')
    @vite(['resources/js/assignment-list.js'])
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.assignment-status-select').forEach(function(select) {
            select.addEventListener('change', function() {
                const assignmentId = this.dataset.assignmentId;
                const newStatus = this.value;
                fetch(`/teacher/assignments/${assignmentId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Статус обновлён!');
                    } else {
                        alert('Ошибка при обновлении статуса');
                    }
                })
                .catch(() => alert('Ошибка при обновлении статуса'));
            });
        });
    });
    </script>
@endpush