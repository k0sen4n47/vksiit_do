@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Задания для группы {{ $group->name }} ({{ $subject->name }})</h4>
                    <a href="{{ route('teacher.assignments.create', ['subjectId' => $subject->id, 'groupId' => $group->id]) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Создать задание
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($assignments->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-muted">Заданий пока нет</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Название</th>
                                        <th>Статус</th>
                                        <th>Срок сдачи</th>
                                        <th>Страниц</th>
                                        <th>Файлов</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                        <tr>
                                            <td>
                                                <a href="{{ route('teacher.assignments.show', ['subject' => $subject->id, 'group' => $group->id, 'assignment' => $assignment->id]) }}">
                                                    {{ $assignment->title }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="assignment-actions">
                                                    <select class="form-select assignment-status-select" data-assignment-id="{{ $assignment->id }}">
                                                        <option value="active" {{ $assignment->status == 'active' ? 'selected' : '' }}>Активное</option>
                                                        <option value="completed" {{ $assignment->status == 'completed' ? 'selected' : '' }}>Завершено</option>
                                                        <option value="archived" {{ $assignment->status == 'archived' ? 'selected' : '' }}>В архиве</option>
                                                    </select>
                                                    <div class="btn-group" style="margin-top: 8px;">
                                                        <a href="{{ route('teacher.assignments.edit', ['subject' => $subject->id, 'group' => $group->id, 'assignment' => $assignment->id]) }}" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('teacher.assignments.destroy', ['subject' => $subject->id, 'group' => $group->id, 'assignment' => $assignment->id]) }}" 
                                                              method="POST" 
                                                              class="d-inline"
                                                              onsubmit="return confirm('Вы уверены, что хотите удалить это задание?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $assignment->deadline->format('d.m.Y H:i') }}</td>
                                            <td>{{ $assignment->pages->count() }}</td>
                                            <td>{{ $assignment->files->count() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $assignments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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