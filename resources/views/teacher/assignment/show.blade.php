@extends('layouts.app')

@section('content')
<div class="assignment-view__container">
    <div class="assignment-view__header">
        <div>
            <i class="fas fa-book-open" style="font-size: 2.1em; color: var(--primary, #3b3bff);"></i>
            <h1 class="assignment-view__title" style="font-size: 2.2em; color: var(--primary, #2c3e50); margin-bottom: 0; font-weight: 700;">{{ $assignment->title }}</h1>
        </div>
        <div class="assignment-view__actions">
            <a href="{{ route('teacher.assignments.edit', ['assignment' => $assignment->id]) }}" class="btn btn-primary btn-lg">
                <i class="fas fa-edit"></i> Редактировать
            </a>
            <form action="{{ route('teacher.assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Вы уверены, что хотите удалить это задание?')">
                    <i class="fas fa-trash"></i> Удалить
                </button>
            </form>
        </div>
    </div>
    <hr>
    <div class="assignment-view__body">
        <div class="assignment-view__section assignment-view__section--description">
            <h2 class="assignment-view__subtitle">Описание</h2>
            <p class="assignment-view__description">{{ $assignment->description }}</p>
        </div>
        <div class="assignment-view__section assignment-view__section--meta">
            <div class="assignment-view__meta-item">
                <span class="assignment-view__meta-label">Предмет</span><br>
                <span class="assignment-view__meta-value">{{ $assignment->subject->name }}</span>
            </div>
            <div class="assignment-view__meta-item">
                <span class="assignment-view__meta-label">Группа</span><br>
                <span class="assignment-view__meta-value">{{ $assignment->primaryGroup->name ?? '' }}</span>
            </div>
            <div class="assignment-view__meta-item">
                <span class="assignment-view__meta-label">Срок сдачи</span><br>
                <span class="assignment-view__meta-value">{{ optional($assignment->deadline)->format('d.m.Y H:i') }}</span>
            </div>
        </div>
        @if($assignment->files->count() > 0)
        <div class="assignment-view__section assignment-view__section--files">
            <h2 class="assignment-view__subtitle"><i class="fas fa-paperclip"></i> Прикрепленные файлы</h2>
            <div class="assignment-view__files-list">
                @foreach($assignment->files as $file)
                <div class="assignment-view__file-item">
                    <i class="fas fa-file" style="color: var(--primary, #3b3bff);"></i>
                    <span>{{ $file->original_name }}</span>
                    <a href="{{ Storage::url($file->path) }}" class="btn btn-sm btn-primary" download>
                        <i class="fas fa-download"></i> Скачать
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        <div class="assignment-view__section assignment-view__section--pages">
            <h2 class="assignment-view__subtitle"><i class="fas fa-layer-group"></i> Страницы задания</h2>
            <div class="assignment-view__pages-list">
                @foreach($assignment->pages as $page)
                <div class="assignment-view__pages-list__text">
                    <div class="assignment-view__page-title">{{ $page->content['title'] ?? '' }}</div>
                    @switch($page->type)
                    @case('text')
                    <div class="assignment-view__page-content">{!! $page->content['text'] ?? '' !!}</div>
                    @break
                    @case('code')
                    <div class="assignment-create__page" data-page-type="code">
                        @php
                        $html = '';
                        $css = '';
                        $title = '';
                        $description = '';
                        if (is_string($page->content)) {
                        $decoded = json_decode($page->content, true);
                        if (is_array($decoded)) {
                        $html = $decoded['html'] ?? '';
                        $css = $decoded['css'] ?? '';
                        $title = $decoded['title'] ?? '';
                        $description = $decoded['description'] ?? '';
                        }
                        } elseif (is_array($page->content)) {
                        $html = $page->content['html'] ?? '';
                        $css = $page->content['css'] ?? '';
                        $title = $page->content['title'] ?? '';
                        $description = $page->content['description'] ?? '';
                        }
                        @endphp
                        @if($description)
                        <div class="assignment-create__description">{{ $description }}</div>
                        @endif
                        <div class="editor-three-panel">
                            <div class="form-group__code-wrapper">
                                <div class="assignment-create__code-wrapper">
                                    <div class="assignment-create__form-group form-group">
                                        <label class="assignment-create__label">HTML</label>
                                        <div class="html-editor"></div>
                                        <textarea class="html-textarea" style="display: none;">{{ $html }}</textarea>
                                    </div>
                                    <div class="assignment-create__form-group form-group">
                                        <label class="assignment-create__label">CSS</label>
                                        <div class="css-editor"></div>
                                        <textarea class="css-textarea" style="display: none;">{{ $css }}</textarea>
                                    </div>
                                </div>
                                <div class="preview-panel">
                                    <div class="assignment-create__form-group form-group">
                                        <label class="assignment-create__label">Предпросмотр</label>
                                        <iframe class="preview-frame"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @break
                    @case('test')
                    <div>
                        <strong>{{ $page->content['title'] ?? 'Тест' }}</strong>
                        <p>{{ $page->content['description'] ?? '' }}</p>
                        @if(isset($page->content['questions']) && is_array($page->content['questions']))
                        <ol>
                            @foreach($page->content['questions'] as $question)
                            <li>
                                <div>{{ $question['text'] ?? '' }}</div>
                                @if(isset($question['answers']) && is_array($question['answers']))
                                <ul>
                                    @foreach($question['answers'] as $answer)
                                    <li>{{ $answer['text'] ?? $answer }}</li>
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                            @endforeach
                        </ol>
                        @endif
                    </div>
                    @break
                    @case('file')
                    <div>{{ $page->content['description'] ?? '' }}</div>
                    @if(isset($page->content['files']))
                    @foreach($page->content['files'] as $file)
                    <a href="{{ Storage::url($file['path']) }}" class="btn btn-primary btn-sm" download>{{ $file['name'] }}</a>
                    @endforeach
                    @endif
                    @break
                    @case('presentation')
                    <div>{{ $page->content['description'] ?? '' }}</div>
                    @if(isset($page->content['slides']))
                    <div>
                        @foreach($page->content['slides'] as $slide)
                        <div>{{ $slide['title'] }}</div>
                        <div>{!! $slide['content'] !!}</div>
                        @endforeach
                    </div>
                    @endif
                    @break
                    @endswitch
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Ответы студентов -->
@if($studentAnswers->count() > 0)
<div class="assignment-view__student-answers">
    <h3>Ответы студентов ({{ $studentAnswers->count() }})</h3>
    <ul>
        @foreach($studentAnswers as $studentAnswer)
        <li>
            <a href="{{ route('teacher.assignments.student-answer.show', ['assignment' => $assignment->id, 'answer' => $studentAnswer->id]) }}" style="text-decoration: underline; cursor: pointer;">
                {{ $studentAnswer->student->fio }}
            </a>
            <span style="color: #888; font-size: 0.95em; margin-left: 10px;">отправлено: {{ $studentAnswer->submitted_at->format('d.m.Y H:i') }}</span>
        </li>
        @endforeach
    </ul>
</div>
@else
<div class="assignment-view__no-answers">
    <i class="fas fa-inbox" style="font-size: 3em; color: #6c757d; margin-bottom: 15px;"></i>
    <h4 style="margin-bottom: 10px;">Пока нет ответов</h4>
    <p>Студенты еще не отправили ответы на это задание.</p>
</div>
@endif
@endsection

@push('styles')
<!-- CodeMirror CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/material.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/eclipse.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-python.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closebrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closetag.min.js"></script>
<script src="/js/assignment-show.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация редакторов кода для просмотра ответов студентов
        function initStudentCodeViewers() {
            const codeViewers = document.querySelectorAll('.code-editor-view');

            codeViewers.forEach(function(viewer) {
                const code = viewer.dataset.code;
                const language = viewer.dataset.language;

                // Создаем редактор только для чтения
                const editor = CodeMirror(viewer, {
                    mode: language,
                    theme: 'default',
                    lineNumbers: true,
                    readOnly: true,
                    value: code,
                    lineWrapping: true,
                    viewportMargin: Infinity
                });

                // Автоматически подстраиваем высоту под содержимое
                setTimeout(() => {
                    const height = editor.getScrollInfo().height;
                    editor.setSize(null, Math.min(height + 20, 300));
                }, 100);
            });

            // Инициализация превью кода студентов
            const codePreviews = document.querySelectorAll('.student-code-preview');

            codePreviews.forEach(function(preview) {
                const answerCard = preview.closest('.student-answer-card');
                const htmlCode = answerCard.querySelector('[data-language="htmlmixed"]');
                const cssCode = answerCard.querySelector('[data-language="css"]');

                if (htmlCode || cssCode) {
                    const html = htmlCode ? htmlCode.dataset.code : '';
                    const css = cssCode ? cssCode.dataset.code : '';

                    const doc = preview.contentDocument || preview.contentWindow.document;
                    doc.open();
                    doc.write(`<!DOCTYPE html><html><head><style>${css}</style></head><body>${html}</body></html>`);
                    doc.close();
                }
            });
        }

        // Инициализируем после загрузки страницы
        setTimeout(initStudentCodeViewers, 500);
    });
</script>
@endpush