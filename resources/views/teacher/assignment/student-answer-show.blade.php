@extends('layouts.app')

@section('content')
<div class="student-answer-view__container">

    <div class="student-answer-view__header">
        <div class="student-answer-view__header-left">
            <h2 class="student-answer-view__title">Ответ студента: <span class="student-answer-view__fio">{{ $studentAnswer->student->fio }}</span></h2>
            <div class="student-answer-view__submitted">Отправлено: {{ $studentAnswer->submitted_at->format('d.m.Y H:i') }}</div>
        </div>
        <a href="{{ route('teacher.assignments.show', $assignment->id) }}" class="btn student-answer-view__button">← Назад к списку ответов</a>
    </div>
    <div class="student-answer-view__content">
        @if($studentAnswer->answer_text)
        <div class="student-answer-view__section student-answer-view__section--text">
            <h5 class="student-answer-view__label">Текстовый ответ:</h5>
            <div class="student-answer-view__text">{!! $studentAnswer->answer_text !!}</div>
        </div>
        @endif
        @if($studentAnswer->answer_html || $studentAnswer->answer_css)
        <div class="student-answer-view__section student-answer-view__section--code">
            <h5 class="student-answer-view__label">Код студента:</h5>
            <div class="editor-three-panel student-code-editor-full">
                <div class="form-group__code-wrapper">
                    <div class="assignment-create__code-wrapper">
                        <div class="assignment-create__form-group form-group">
                            <label class="assignment-create__label">HTML</label>
                            <div class="html-editor"></div>
                            <textarea class="html-textarea" style="display: none;">{{ $studentAnswer->answer_html }}</textarea>
                        </div>
                        <div class="assignment-create__form-group form-group">
                            <label class="assignment-create__label">CSS</label>
                            <div class="css-editor"></div>
                            <textarea class="css-textarea" style="display: none;">{{ $studentAnswer->answer_css }}</textarea>
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
        @endif
        @if($studentAnswer->files)
        @php
        $files = is_array($studentAnswer->files) ? $studentAnswer->files : (is_string($studentAnswer->files) ? json_decode($studentAnswer->files, true) : []);
        @endphp
        @if($files && count($files))
        <div class="student-answer-view__section student-answer-view__section--files">
            <h5 class="student-answer-view__label">Прикрепленные файлы:</h5>
            <ul class="student-answer-view__files-list">
                @foreach($files as $file)
                <li class="student-answer-view__file-item">
                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="student-answer-view__file-link">
                        <i class="fas fa-file student-answer-view__file-icon"></i>{{ basename($file) }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closebrackets.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closetag.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация readonly CodeMirror для просмотра кода студента
    const htmlEditorDiv = document.querySelector('.html-editor');
    const cssEditorDiv = document.querySelector('.css-editor');
    const htmlTextarea = document.querySelector('.html-textarea');
    const cssTextarea = document.querySelector('.css-textarea');
    const previewFrame = document.querySelector('.preview-frame');
    if (htmlEditorDiv && cssEditorDiv && htmlTextarea && cssTextarea) {
        const htmlEditor = CodeMirror(htmlEditorDiv, {
            mode: 'htmlmixed',
            theme: 'default',
            lineNumbers: true,
            readOnly: true,
            value: htmlTextarea.value || '',
            lineWrapping: true,
            viewportMargin: Infinity
        });
        const cssEditor = CodeMirror(cssEditorDiv, {
            mode: 'css',
            theme: 'default',
            lineNumbers: true,
            readOnly: true,
            value: cssTextarea.value || '',
            lineWrapping: true,
            viewportMargin: Infinity
        });
        // Предпросмотр
        function updatePreview() {
            if (!previewFrame) return;
            const html = htmlEditor.getValue();
            const css = cssEditor.getValue();
            const doc = previewFrame.contentDocument || previewFrame.contentWindow.document;
            doc.open();
            doc.write(`<!DOCTYPE html><html><head><style>${css}</style></head><body>${html}</body></html>`);
            doc.close();
        }
        updatePreview();
    }
});
</script>
@endpush