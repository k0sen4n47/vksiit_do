@extends('admin.dashboard')

@section('admin_content')
<div class="dashboard__create-content">
    <h2>Создать новое задание</h2>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.assignments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-create">
            <div class="form-create__top">
                <div class="form-group">
                    <label for="subject_id">Предмет:</label>
                    <select name="subject_id" id="subject_id" class="form-control" required>
                        <option value="">Выберите предмет</option>
                        @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} ({{ $subject->abbreviation }})
                        </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="title">Название задания:</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                    @error('title')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Описание задания:</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                    @error('description')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="deadline">Срок сдачи:</label>
                    <input type="datetime-local" name="deadline" id="deadline" class="form-control" value="{{ old('deadline') }}" required>
                    @error('deadline')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="max_score">Максимальный балл:</label>
                    <input type="number" name="max_score" id="max_score" class="form-control" value="{{ old('max_score', 100) }}" min="1" max="100" required>
                    @error('max_score')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="files">Файлы задания:</label>
                    <input type="file" name="files[]" id="files" class="form-control" multiple>
                    <small class="form-text text-muted">Вы можете выбрать несколько файлов</small>
                    @error('files')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="groups">Группы:</label>
                    <select name="groups[]" id="groups" class="form-control" multiple required>
                        @foreach ($groups as $group)
                        <option value="{{ $group->id }}" {{ in_array($group->id, old('groups', [])) ? 'selected' : '' }}>
                            {{ $group->short_name }}-{{ $group->course }}{{ $group->year }}@if(!empty($group->suffix)) {{ $group->suffix }}@endif
                        </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">Удерживайте Ctrl для выбора нескольких групп</small>
                    @error('groups')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Создать задание</button>
        </div>
    </form>
</div>

@push('styles')
<style>
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-control:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

textarea.form-control {
    resize: vertical;
}

select[multiple].form-control {
    height: 150px;
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.alert {
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Добавляем валидацию на стороне клиента
    const form = document.querySelector('form');
    const deadlineInput = document.getElementById('deadline');
    const maxScoreInput = document.getElementById('max_score');

    // Устанавливаем минимальную дату на текущий момент
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    deadlineInput.min = now.toISOString().slice(0, 16);

    // Валидация максимального балла
    maxScoreInput.addEventListener('input', function() {
        if (this.value < 1) this.value = 1;
        if (this.value > 100) this.value = 100;
    });

    // Валидация формы перед отправкой
    form.addEventListener('submit', function(e) {
        const groups = document.getElementById('groups');
        if (groups.selectedOptions.length === 0) {
            e.preventDefault();
            alert('Пожалуйста, выберите хотя бы одну группу');
        }
    });
});
</script>
@endpush
@endsection 