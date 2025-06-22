@extends('admin.dashboard')

@section('admin_content')
<div class="dashboard__create-content">
    <h3>Создать новый предмет</h3>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.subjects.store') }}" method="POST" id="subjectForm" enctype="multipart/form-data">
        @csrf
        <div class="form-create">
            <div class="form-create__top">
                <div class="form-group">
                    <label for="name">Название предмета:</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="abbreviation">Аббревиатура предмета:</label>
                    <input type="text" name="abbreviation" id="abbreviation" class="form-control" value="{{ old('abbreviation') }}" required>
                    @error('abbreviation')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="image">Изображение предмета:</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    @error('image')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <h3>Назначение преподавателей и групп</h3>

                <div id="connections-container">
                    <div class="form-create__connection" data-index="0">
                        <div class="form-create__connection-header">
                            <h2>Связь 1</h2>
                            <button type="button" class="btn btn-danger btn-sm form-create__connection-remove" style="display: none;">Удалить</button>
                        </div>

                        <div class="form-group">
                            <label for="connections[0][teacher_id]">Преподаватель:</label>
                            <select name="connections[0][teacher_id]" id="connections[0][teacher_id]" class="form-control teacher-select">
                                <option value="">Выберите преподавателя</option>
                                @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('connections.0.teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->fio }}</option>
                                @endforeach
                            </select>
                            @error('connections.0.teacher_id')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="connections[0][group_ids]">Группы:</label>
                            <select name="connections[0][group_ids][]" id="connections[0][group_ids]" class="form-control group-select" multiple>
                                @foreach ($groups as $group)
                                <option value="{{ $group->id }}" {{ in_array($group->id, old('connections.0.group_ids', [])) ? 'selected' : '' }}>
                                    {{ $group->short_name }}-{{ $group->course }}{{ $group->year }}@if(!empty($group->suffix)) {{ $group->suffix }}@endif
                                </option>
                                @endforeach
                            </select>
                            @error('connections.0.group_ids')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-create__actions">
                    <button type="button" class="btn btn-secondary" id="addConnection">Добавить связь</button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Создать предмет</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('connections-container');
    const addButton = document.getElementById('addConnection');
    let connectionCount = 1;

    // Функция для создания новой связи
    function createConnection(index) {
        const template = `
            <div class="form-create__connection" data-index="${index}">
                <div class="form-create__connection-header">
                    <h2>Связь ${index + 1}</h2>
                    <button type="button" class="btn btn-danger btn-sm form-create__connection-remove">Удалить</button>
                </div>

                <div class="form-group">
                    <label for="connections[${index}][teacher_id]">Преподаватель:</label>
                    <select name="connections[${index}][teacher_id]" id="connections[${index}][teacher_id]" class="form-control teacher-select">
                        <option value="">Выберите преподавателя</option>
                        @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->fio }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="connections[${index}][group_ids]">Группы:</label>
                    <select name="connections[${index}][group_ids][]" id="connections[${index}][group_ids]" class="form-control group-select" multiple>
                        @foreach ($groups as $group)
                        <option value="{{ $group->id }}">
                            {{ $group->short_name }}-{{ $group->course }}{{ $group->year }}@if(!empty($group->suffix)) {{ $group->suffix }}@endif
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        `;
        return template;
    }

    // Добавление новой связи
    addButton.addEventListener('click', function() {
        const newConnection = createConnection(connectionCount);
        container.insertAdjacentHTML('beforeend', newConnection);
        connectionCount++;

        // Показываем кнопку удаления для первой связи, если есть больше одной связи
        if (connectionCount > 1) {
            document.querySelector('.form-create__connection-remove').style.display = 'inline-block';
        }
    });

    // Удаление связи
    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('form-create__connection-remove')) {
            const connection = e.target.closest('.form-create__connection');
            connection.remove();
            connectionCount--;

            // Скрываем кнопку удаления для первой связи, если осталась только одна связь
            if (connectionCount === 1) {
                document.querySelector('.form-create__connection-remove').style.display = 'none';
            }

            // Обновляем номера связей
            document.querySelectorAll('.form-create__connection').forEach((conn, index) => {
                conn.querySelector('h4').textContent = `Связь #${index + 1}`;
                conn.dataset.index = index;
                
                // Обновляем name и id для select элементов
                const teacherSelect = conn.querySelector('.teacher-select');
                const groupSelect = conn.querySelector('.group-select');
                
                teacherSelect.name = `connections[${index}][teacher_id]`;
                teacherSelect.id = `connections[${index}][teacher_id]`;
                
                groupSelect.name = `connections[${index}][group_ids][]`;
                groupSelect.id = `connections[${index}][group_ids]`;
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-create__connection {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 5px;
}

.form-create__connection-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.form-create__connection-header h4 {
    margin: 0;
}

.form-create__actions {
    margin: 20px 0;
}

.group-select {
    height: 150px;
}
</style>
@endpush
@endsection