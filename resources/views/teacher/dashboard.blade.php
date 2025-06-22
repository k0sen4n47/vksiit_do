@extends('layouts.app')

@section('title-page')
Панель преподавателя
@endsection

@section('content')
<div class="teacher-dashboard teacher-dashboard--main">
    <div class="teacher-dashboard__content teacher-dashboard__content--main">
        <h1 class="teacher-dashboard__title">Добро пожаловать, Преподаватель!</h1>
        <!-- <div class="teacher-dashboard__info-text">
            <p>
                В этом разделе вы можете управлять своими заданиями, создавать новые, просматривать и редактировать существующие, а также отслеживать успехи студентов. Используйте кнопки ниже для быстрого перехода к нужному разделу.
            </p>
        </div> -->

    </div>
    <div class="teacher-dashboard__profile">
        <div class="teacher-dashboard__profile-main">
            <div class="teacher-dashboard__profile-photo-block">
                <img src="{{ $teacher->photo_url }}" alt="Фото преподавателя" class="teacher-dashboard__profile-photo">
                <form action="{{ route('teacher.upload-photo') }}" method="POST" enctype="multipart/form-data" class="teacher-dashboard__profile-form">
                    @csrf
                    <label class="teacher-dashboard__profile-file-label" style="cursor:pointer;">
                        <input type="file" name="photo" accept="image/*" class="teacher-dashboard__profile-input" style="display:none;" onchange="showTeacherFileNameAndPreview(event)">
                        <span class="teacher-dashboard__profile-file-btn" tabindex="0" style="cursor:pointer;">Выбрать файл</span>
                        <span id="teacherFileName" style="margin-left:10px;"></span>
                    </label>
                    <img id="teacherImagePreview" style="max-width: 200px; display: none; margin-top:10px;" />
                    <button type="submit" class="btn-cabinet teacher-dashboard__profile-btn" id="teacherUploadBtn" disabled>Загрузить фото</button>
                </form>
            </div>
            <div class="teacher-dashboard__profile-info">
                <div class="teacher-dashboard__profile-row"><span class="teacher-dashboard__profile-label">ФИО:</span> <span class="teacher-dashboard__profile-value">{{ $teacher->fio ?? $teacher->name }}</span></div>
                <div class="teacher-dashboard__profile-row"><span class="teacher-dashboard__profile-label">Почта:</span> <span class="teacher-dashboard__profile-value">{{ $teacher->email ?? '-' }}</span></div>
                <div class="teacher-dashboard__profile-row"><span class="teacher-dashboard__profile-label">Логин:</span> <span class="teacher-dashboard__profile-value">{{ $teacher->login ?? '-' }}</span></div>
                @if($curatorGroup)
                <div class="teacher-dashboard__profile-row">
                    <span class="teacher-dashboard__profile-label">Куратор группы:</span>
                    <span class="teacher-dashboard__profile-value">
                        {{ $curatorGroup->short_name }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="teacher-dashboard__nav-buttons">
        <a href="{{ route('teacher.dashboard') }}" class="teacher-dashboard__nav-btn btn-cabinet @if(request()->routeIs('teacher.dashboard')) active @endif">Главная</a>
        <a href="{{ route('teacher.assignments.index') }}" class="teacher-dashboard__nav-btn btn-cabinet @if(request()->routeIs('teacher.assignments.index')) active @endif">Мои задания</a>
        <a href="{{ route('teacher.assignments.subjects.index') }}" class="teacher-dashboard__nav-btn btn-cabinet @if(request()->routeIs('teacher.assignments.subjects.*')) active @endif">Добавить задание</a>
    </div>
</div>
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
@endsection

@push('scripts')
<script src="/js/tinymce/js/tinymce/tinymce.min.js"></script>
<script>
    function showTeacherFileNameAndPreview(event) {
        const input = event.target;
        const fileNameSpan = document.getElementById('teacherFileName');
        const imagePreview = document.getElementById('teacherImagePreview');
        const uploadBtn = document.getElementById('teacherUploadBtn');
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