@extends('layouts.app')

@section('title-page')
Личный кабинет студента
@endsection

@section('content')
<div class="student-dashboard">
    <div class="student-dashboard__header">
        <h1 class="student-dashboard__title">Добро пожаловать, студент!</h1>
        <div class="student-dashboard__profile">
            <div class="student-dashboard__profile-main">
                <div class="student-dashboard__profile-photo-block">
                    <img src="{{ $student->photo_url }}" alt="Фото студента" class="student-dashboard__profile-photo">
                    <form action="{{ route('student.upload-photo') }}" method="POST" enctype="multipart/form-data" class="student-dashboard__profile-form">
                        @csrf
                        <label class="student-dashboard__profile-file-label" style="cursor:pointer;">
                            <input type="file" name="photo" accept="image/*" class="student-dashboard__profile-input" style="display:none;" onchange="showStudentFileNameAndPreview(event)">
                            <span class="student-dashboard__profile-file-btn" tabindex="0" style="cursor:pointer;">Выбрать файл</span>
                            <span id="studentFileName" style="margin-left:10px;"></span>
                        </label>
                        <img id="studentImagePreview" style="max-width: 200px; display: none; margin-top:10px;" />
                        <button type="submit" class="btn-cabinet student-dashboard__profile-btn" id="studentUploadBtn" disabled>Загрузить фото</button>
                    </form>
                </div>
                <div class="student-dashboard__profile-info">
                    <div class="student-dashboard__profile-row"><span class="student-dashboard__profile-label">ФИО:</span> <span class="student-dashboard__profile-value">{{ $student->fio ?? 'Студент' }}</span></div>
                    <div class="student-dashboard__profile-row"><span class="student-dashboard__profile-label">Логин:</span> <span class="student-dashboard__profile-value">{{ $student->login ?? '-' }}</span></div>
                    <div class="student-dashboard__profile-row"><span class="student-dashboard__profile-label">Группа:</span> <span class="student-dashboard__profile-value">{{ $student->group ? ($student->group->short_name . '-' . $student->group->course . $student->group->year . ($student->group->suffix ? ' ' . $student->group->suffix : '')) : '-' }}</span></div>
                    <div class="student-dashboard__profile-row"><span class="student-dashboard__profile-label">Куратор:</span> <span class="student-dashboard__profile-value">{{ $student->group && $student->group->curator ? ($student->group->curator->fio ?? $student->group->curator->name) : '-' }}</span></div>
                    <div class="student-dashboard__profile-row"><span class="student-dashboard__profile-label">Почта:</span> <span class="student-dashboard__profile-value">{{ $student->email ?? '-' }}</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="student-dashboard__content">
        {{-- Здесь будет список предметов --}}
        <!-- <div class="student-dashboard__subjects-empty">
            <p>Здесь появится список ваших предметов.</p>
        </div> -->
        @if(isset($upcomingAssignments) && $upcomingAssignments->count())
            <div class="student-dashboard__upcoming">
                <div class="student-dashboard__upcoming-title">Ближайшие задания</div>
                <ul class="student-dashboard__upcoming-list">
                    @foreach($upcomingAssignments as $assignment)
                        <li class="student-dashboard__upcoming-item">
                            <div class="student-dashboard__upcoming-main">
                                <span class="student-dashboard__upcoming-name">{{ $assignment->title }}</span>
                                <span class="student-dashboard__upcoming-subject">{{ $assignment->subject->name ?? 'Без предмета' }}</span>
                            </div>
                            <div class="student-dashboard__upcoming-meta">
                                <span class="student-dashboard__upcoming-deadline">до {{ $assignment->deadline ? \Carbon\Carbon::parse($assignment->deadline)->format('d.m.Y H:i') : '-' }}</span>
                                <a href="{{ route('student.assignments.show', $assignment->id) }}" class="student-dashboard__upcoming-link btn-cabinet">Открыть</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(isset($upcomingAssignments) && $upcomingAssignments)
            <pre>{{ print_r($upcomingAssignments, true) }}</pre>
        @endif
    </div>
    <div class="student-dashboard__nav">
        <a href="{{ route('student.subjects.index') }}" class="btn-cabinet btn-cabinet--active">Предметы</a>
        {{-- В будущем можно добавить другие вкладки --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('.student-dashboard__profile-input');
    const fileBtn = document.querySelector('.student-dashboard__profile-file-btn');
    if (fileBtn && fileInput) {
        fileBtn.addEventListener('click', function(e) {
            fileInput.click();
        });
    }
});

function showStudentFileNameAndPreview(event) {
    const input = event.target;
    const fileNameSpan = document.getElementById('studentFileName');
    const imagePreview = document.getElementById('studentImagePreview');
    const uploadBtn = document.getElementById('studentUploadBtn');
    if (input.files && input.files[0]) {
        fileNameSpan.textContent = input.files[0].name;
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
        if (uploadBtn) uploadBtn.disabled = false;
    } else {
        fileNameSpan.textContent = 'Файл не выбран';
        imagePreview.style.display = 'none';
        if (uploadBtn) uploadBtn.disabled = true;
    }
}
</script>
@endpush

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif