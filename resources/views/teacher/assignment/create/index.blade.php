@extends('layouts.app')

@section('title', 'Создание задания')

@section('styles')
<!-- CodeMirror CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/material.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/solarized.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/eclipse.min.css">
<!-- CodeMirror Addons CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/foldgutter.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/hint/show-hint.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/search/matchesonscrollbar.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Fira Code Font -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500&display=swap">
<!-- TinyMCE -->
<script src="/js/tinymce/js/tinymce/tinymce.min.js"></script>

@vite(['resources/css/pages/assignment.css'])


@endsection

@section('content')
<div class="assignment-create">
    <div class="assignment-create__card">
        <div class="assignment-create__header">
            <h1 class="assignment-create__title">Создание задания</h1>
            @if($selectedSubject || $selectedGroup)
            <div class="assignment-create__info">
                <div class="assignment-create__info-item-wrapper">
                    @if($selectedSubject)
                    <span class="assignment-create__info-item">
                        <strong>Предмет:</strong> {{ $selectedSubject->name }}
                    </span>
                    @endif
                    @if($selectedGroup)
                    <span class="assignment-create__info-item">
                        <strong>Группа:</strong> {{ $selectedGroup->name }}
                    </span>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">
                    <i class="fas fa-edit"></i> Изменить
                </button>
            </div>
            @endif
        </div>
        <div class="assignment-create__body">
            <form id="assignmentForm" method="POST" action="{{ route('teacher.assignments.store') }}" class="assignment-create__form">
                @csrf

                <div class="assignment-create__form-group form-group">
                    <label for="title" class="assignment-create__label">Название</label>
                    <input type="text" class="assignment-create__input" id="title" name="title" required>
                </div>

                <div class="assignment-create__form-group form-group">
                    <label for="description" class="assignment-create__label">Описание</label>
                    <textarea class="assignment-create__textarea" id="description" name="description" rows="3"></textarea>
                </div>

                <div class="assignment-create__form-group form-group" id="subject-group" style="{{ $selectedSubject ? 'display: none;' : '' }}">
                    <label for="subject_id" class="assignment-create__label">Предмет</label>
                    <select class="assignment-create__input" id="subject_id" name="subject_id" {{ !$selectedSubject ? 'required' : '' }}>
                        <option value="">Выберите предмет</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ $selectedSubject && $selectedSubject->id == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedSubject)
                <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                @endif

                <div class="assignment-create__form-group form-group" id="group-group" style="{{ $selectedGroup ? 'display: none;' : '' }}">
                    <label for="group_id" class="assignment-create__label">Группа</label>
                    <select class="assignment-create__input" id="group_id" name="group_id" {{ !$selectedGroup ? 'required' : '' }}>
                        <option value="">Выберите группу</option>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ $selectedGroup && $selectedGroup->id == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedGroup)
                <input type="hidden" name="group_id" value="{{ $selectedGroup->id }}">
                @endif

                <div class="assignment-create__form-group form-group">
                    <label for="deadline" class="assignment-create__label">Срок сдачи</label>
                    <input type="datetime-local" class="assignment-create__input" id="deadline" name="deadline" required>
                </div>

                <div class="assignment-create__pages">
                    <!-- Здесь будут страницы задания -->
                </div>

                <button type="button" class="assignment-create__add-page btn">
                    <i class="fas fa-plus"></i> Добавить страницу
                </button>

                <div class="save-wrapper__button">
                    <button type="submit" class="assignment-create__submit">
                        <i class="fas fa-save"></i> Создать задание
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Шаблоны страниц -->
<template id="textPageTemplate">
    <div class="assignment-create__page" data-page-type="text">
        <div class="assignment-create__page-header">
            <h3 class="assignment-create__page-title">Текстовая страница</h3>
            <button type="button" class="assignment-create__remove-page">
                <i class="fas fa-times"></i> Удалить
            </button>
        </div>
        <div class="assignment-create__page-body">
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Заголовок</label>
                <input type="text" class="assignment-create__input" name="pages[PAGE_INDEX][title]" required>
            </div>
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Описание</label>
                <textarea id="tinymce-editor-page-[PAGE_INDEX]" class="assignment-create__textarea tinymce-editor" name="pages[PAGE_INDEX][content]" rows="3"></textarea>
            </div>
        </div>
    </div>
</template>
@include('teacher.assignment.create.partials._presentation_page_template')
@include('teacher.assignment.create.partials._file_page_template')

<template id="codePageTemplate">
    <div class="assignment-create__page" data-page-index="[PAGE_INDEX]" data-page-type="code">
        <div class="assignment-create__page-header">
            <h3 class="assignment-create__page-title">Страница [PAGE_INDEX]</h3>
            <button type="button" class="assignment-create__page-remove" data-page-index="[PAGE_INDEX]">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="assignment-create__page-body">
            <div class="assignment-create__form-group form-group">
                <label for="page_title_[PAGE_INDEX]" class="assignment-create__label">Заголовок страницы</label>
                <input type="text" id="page_title_[PAGE_INDEX]" name="pages[[PAGE_INDEX]][title]" class="assignment-create__input" required>
            </div>
            <div class="assignment-create__form-group form-group">
                <label for="page_description_[PAGE_INDEX]" class="assignment-create__label">Описание задания</label>
                <textarea id="page_description_[PAGE_INDEX]" name="pages[[PAGE_INDEX]][description]" class="assignment-create__textarea" rows="3"></textarea>
            </div>
            <div class="assignment-create__form-group assignment-create__edit-code">
                <div class="assignment-create__form-group assignment-create__theme-code">
                    <label class="assignment-create__label">Настройки редактора</label>
                    <div class="assignment-create__code-toolbar">
                        <select class="theme-selector">
                            <option value="default">Светлая тема</option>
                            <option value="dracula">Dracula</option>
                            <option value="monokai">Monokai</option>
                            <option value="material">Material</option>
                        </select>
                    </div>
                </div>
                <div class="assignment-create__code-toolbar">
                    <button type="button" class="download-zip">
                        <i class="fas fa-download"></i> Скачать как архив
                    </button>
                </div>
            </div>
            <div class="editor-three-panel">
                <div class="form-group__code-wrapper">
                    <div class="assignment-create__code-wrapper">
                        <div class="assignment-create__form-group form-group">
                            <label class="assignment-create__label">HTML</label>
                            <div class="html-editor"></div>
                            <textarea name="pages[[PAGE_INDEX]][html]" style="display: none;"></textarea>
                        </div>
                        <div class="assignment-create__form-group form-group">
                            <label class="assignment-create__label">CSS</label>
                            <div class="css-editor"></div>
                            <textarea name="pages[[PAGE_INDEX]][css]" style="display: none;"></textarea>
                        </div>
                    </div>
                    <div class="preview-panel">
                        <div class="assignment-create__form-group form-group">
                            <label class="assignment-create__label">Предпросмотр</label>
                            <iframe class="preview-frame" style="height: 300px; border: 1px solid #ddd;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="testPageTemplate">
    <div class="assignment-create__page test" data-page-index="[PAGE_INDEX]" data-test-index="[PAGE_INDEX]" data-page-type="test">
        <div class="assignment-create__page-header">
            <h3 class="assignment-create__page-title">Страница [PAGE_INDEX]</h3>
            <button type="button" class="assignment-create__page-remove" data-page-index="[PAGE_INDEX]">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="assignment-create__page-body">
            <div class="assignment-create__form-group form-group">
                <label for="test_title_[PAGE_INDEX]" class="assignment-create__label">Название теста</label>
                <input type="text" id="test_title_[PAGE_INDEX]" name="pages[[PAGE_INDEX]][title]" class="assignment-create__input" required>
            </div>
            <div class="assignment-create__form-group form-group">
                <label for="test_description_[PAGE_INDEX]" class="assignment-create__label">Описание теста</label>
                <textarea id="test_description_[PAGE_INDEX]" name="pages[[PAGE_INDEX]][description]" class="assignment-create__textarea" rows="3"></textarea>
            </div>
            <div class="assignment-create__header-test">
                <div class="assignment-create__form-group form-group">
                    <label for="test_time_[PAGE_INDEX]" class="assignment-create__label">Время на выполнение (в минутах)</label>
                    <input type="number" id="test_time_[PAGE_INDEX]" name="pages[[PAGE_INDEX]][time_limit]" class="assignment-create__input" min="1" value="30">
                </div>
                <div class="assignment-create__form-group form-group">
                    <label for="test_passing_[PAGE_INDEX]" class="assignment-create__label">Проходной балл</label>
                    <input type="number" id="test_passing_[PAGE_INDEX]" name="pages[[PAGE_INDEX]][passing_score]" class="assignment-create__input" min="1" value="60">
                </div>
            </div>
            <div class="test-questions">
                <div class="test-questions__header">
                    <h4>Вопросы</h4>
                </div>
                <div class="test-questions__container questions-container">
                    <!-- Здесь будут вопросы -->
                </div>
                <button type="button" class="btn btn-primary add-question" data-page-index="[PAGE_INDEX]">
                    <i class="fas fa-plus"></i> Добавить вопрос
                </button>
            </div>
            @include('teacher.assignment.create.tests.partials.templates')
        </div>
    </div>
</template>

<template id="question-template">
    <div class="test-question">
        <div class="test-question__header">
            <h5>Вопрос <span class="question-number"></span></h5>
            <button type="button" class="btn remove-question">
                <i class="fas fa-times"></i>
                Удалить вопрос
            </button>
        </div>
        <div class="test-question__content">
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Текст вопроса</label>
                <input type="text" class="assignment-create__input question-text" required>
            </div>
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Тип вопроса</label>
                <select class="assignment-create__input question-type" required>
                    <option value="single">Один правильный ответ</option>
                    <option value="multiple">Несколько правильных ответов</option>
                    <option value="text">Текстовый ответ</option>
                </select>
            </div>
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Баллы</label>
                <input type="number" class="assignment-create__input question-score" min="1" value="1" required>
            </div>
            <div class="test-answers">
                <div class="test-answers__header">
                    <h6>Ответы</h6>
                    <button type="button" class="btn btn-primary btn-sm add-answer">
                        <i class="fas fa-plus"></i> Добавить ответ
                    </button>
                </div>
                <div class="test-answers__container answers-container">
                    <!-- Здесь будут ответы -->
                </div>
            </div>
        </div>
    </div>
</template>

<template id="answer-template">
    <div class="test-answer">
        <div class="test-answer__content">
            <div class="assignment-create__form-group form-group">
                <div class="test-answer__controls">
                    <div class="form-check">
                        <input type="radio" class="form-check-input answer-correct" value="1">
                        <label class="form-check-label">Правильный ответ</label>
                    </div>
                    <button type="button" class="btn remove-answer">
                        <i class="fas fa-times"></i>
                        Удалить ответ
                    </button>
                </div>
                <input type="text" class="assignment-create__input answer-text" placeholder="Введите текст ответа" required>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<!-- CodeMirror JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/python/python.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/php/php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/clike/clike.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/matchbrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closebrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closetag.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/hint/show-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/hint/javascript-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/hint/html-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/hint/css-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/search/searchcursor.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/search/match-highlighter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/selection/active-line.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/foldcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/foldgutter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/xml-fold.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/brace-fold.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/fold/comment-fold.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/display/fullscreen.min.js"></script>

<!-- Prettier for code formatting -->
<script src="https://unpkg.com/prettier@2.8.8/standalone.js"></script>
<script src="https://unpkg.com/prettier@2.8.8/parser-html.js"></script>
<script src="https://unpkg.com/prettier@2.8.8/parser-css.js"></script>
<script src="https://unpkg.com/prettier@2.8.8/parser-babel.js"></script>

<!-- JSZip library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Assignment Modal JS -->
<!-- @vite(['resources/js/assignment-modal.js']) -->

@vite(['resources/js/assignment-create.js'])
@endpush