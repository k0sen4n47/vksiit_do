@extends('layouts.app')

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
@endsection

@section('content')
<div class="assignment-create__container">
    <div class="assignment-create__header">
        <h1 class="assignment-create__title">Редактирование задания</h1>
    </div>
    <div class="assignment-create__body">
        <form action="{{ route('teacher.assignments.update', $assignment) }}" method="POST" enctype="multipart/form-data" class="assignment-create__form">
            @csrf
            @method('PUT')

            <div class="assignment-create__form-group form-group">
                <label for="title" class="assignment-create__label">Название</label>
                <input type="text" class="assignment-create__input @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $assignment->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="assignment-create__form-group form-group">
                <label for="description" class="assignment-create__label">Описание</label>
                <textarea class="assignment-create__textarea @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description', $assignment->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="assignment-create__form-group form-group">
                <label for="deadline" class="assignment-create__label">Срок сдачи</label>
                <input type="datetime-local" class="assignment-create__input @error('deadline') is-invalid @enderror" id="deadline" name="deadline" value="{{ old('deadline', optional($assignment->deadline)->format('Y-m-d\TH:i')) }}" required>
                @error('deadline')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if($assignment->files->count() > 0)
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Прикрепленные файлы</label>
                <div class="list-group">
                    @foreach($assignment->files as $file)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <input type="checkbox" name="delete_files[]" value="{{ $file->id }}" id="delete_file_{{ $file->id }}">
                            <label for="delete_file_{{ $file->id }}">
                                <i class="fas fa-file"></i>
                                {{ $file->original_name }}
                            </label>
                        </div>
                        <a href="{{ route('files.download', $file) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-download"></i> Скачать
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Страницы задания</label>
                <div id="pages-container">
                    @foreach($assignment->pages as $index => $page)
                    <div class="page-item">
                        <div class="card-header">
                            <h6 class="">Страница {{ $index + 1 }}</h6>
                            <button type="button" class="btn btn-danger btn-sm remove-page">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="assignment-create__form-group form-group">
                                <label class="assignment-create__label">Тип страницы</label>
                                <select class="assignment-create__input form-select page-type" name="pages[{{ $index }}][type]" required>
                                    <option value="text" {{ $page->type === 'text' ? 'selected' : '' }}>Текст</option>
                                    <option value="code" {{ $page->type === 'code' ? 'selected' : '' }}>Код</option>
                                    <option value="test" {{ $page->type === 'test' ? 'selected' : '' }}>Тест</option>
                                </select>
                            </div>
                            <div class="page-content">
                                @switch($page->type)
                                    @case('text')
                                        <div class="assignment-create__form-group form-group">
                                            <label class="assignment-create__label">Текст</label>
                                            <textarea class="assignment-create__textarea tinymce-editor form-control" name="pages[{{ $index }}][content][text]" rows="8">{{ $page->content['text'] ?? '' }}</textarea>
                                        </div>
                                        @break
                                    @case('code')
                                        <div class="editor-three-panel">
                                            <div class="form-group__code-wrapper">
                                                <div class="assignment-create__code-wrapper">
                                                    <div class="assignment-create__form-group form-group">
                                                        <label class="assignment-create__label">HTML</label>
                                                        <div class="html-editor"></div>
                                                        <textarea name="pages[{{ $index }}][content][html]" class="codemirror-editor-html" style="display: none;">{{ $page->content['html'] ?? '' }}</textarea>
                                                    </div>
                                                    <div class="assignment-create__form-group form-group">
                                                        <label class="assignment-create__label">CSS</label>
                                                        <div class="css-editor"></div>
                                                        <textarea name="pages[{{ $index }}][content][css]" class="codemirror-editor-css" style="display: none;">{{ $page->content['css'] ?? '' }}</textarea>
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
                                        @break
                                    @case('test')
                                        <div class='test-page-placeholder'>Тестовая страница (редактирование реализуйте отдельно)</div>
                                        @break
                                    @case('file')
                                        <div class="assignment-create__form-group form-group">
                                            <label class="assignment-create__label">Файл</label>
                                            <input type="file" class="assignment-create__input form-control" name="pages[{{ $index }}][content][file]">
                                        </div>
                                        @break
                                    @case('presentation')
                                        <div class="assignment-create__form-group form-group">
                                            <label class="assignment-create__label">URL презентации</label>
                                            <input type="url" class="assignment-create__input form-control" name="pages[{{ $index }}][content][url]" value="{{ $page->content['url'] ?? '' }}">
                                        </div>
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="assignment-create__add-slide btn btn-secondary" id="add-page">
                    <i class="fas fa-plus"></i> Добавить страницу
                </button>
            </div>

            <div class="assignment-create__form-group form-group edit-task">
                <a href="{{ route('teacher.assignments.show', $assignment) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
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
<!-- TinyMCE -->
<script src="/js/tinymce/js/tinymce/tinymce.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pagesContainer = document.getElementById('pages-container');  
    const addPageButton = document.getElementById('add-page');
    let pageCount = {{ count($assignment->pages) }};

    // Функция для создания новой страницы
    function createPageElement() {
        const pageHtml = `
            <div class="page-item">
                <div class="card-header">
                    <h6 class="">Страница ${pageCount + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-page">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Тип страницы</label>
                        <select class="form-select page-type" name="pages[${pageCount}][type]" required>
                            <option value="text">Текст</option>
                            <option value="code">Код</option>
                            <option value="test">Тест</option>
                        </select>
                    </div>
                    <div class="page-content">
                        <div class="mb-3">
                            <label class="form-label">Текст</label>
                            <textarea class="form-control tinymce-editor" name="pages[${pageCount}][content][text]" rows="5"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;
        pageCount++;
        return pageHtml;
    }

    // Добавление новой страницы
    addPageButton.addEventListener('click', function() {
        pagesContainer.insertAdjacentHTML('beforeend', createPageElement());
        updatePageNumbers();
    });

    // Удаление страницы
    pagesContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-page')) {
            e.target.closest('.page-item').remove();
            updatePageNumbers();
        }
    });

    // Изменение типа страницы
    pagesContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('page-type')) {
            const pageItem = e.target.closest('.page-item');
            const contentContainer = pageItem.querySelector('.page-content');
            const pageIndex = Array.from(pagesContainer.children).indexOf(pageItem);

            let contentHtml = '';
            switch (e.target.value) {
                case 'text':
                    contentHtml = `
                        <div class="mb-3">
                            <label class="form-label">Текст</label>
                            <textarea class="form-control tinymce-editor" name="pages[${pageIndex}][content][text]" rows="5"></textarea>
                        </div>
                    `;
                    break;
                case 'code':
                    contentHtml = `
                        <div class="editor-three-panel">
                            <div class="form-group__code-wrapper">
                                <div class="assignment-create__code-wrapper">
                                    <div class="assignment-create__form-group form-group">
                                        <label class="assignment-create__label">HTML</label>
                                        <div class="html-editor"></div>
                                        <textarea name="pages[${pageIndex}][content][html]" class="codemirror-editor-html" style="display: none;"></textarea>
                                    </div>
                                    <div class="assignment-create__form-group form-group">
                                        <label class="assignment-create__label">CSS</label>
                                        <div class="css-editor"></div>
                                        <textarea name="pages[${pageIndex}][content][css]" class="codemirror-editor-css" style="display: none;"></textarea>
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
                    `;
                    break;
                case 'test':
                    contentHtml = `<div class='test-page-placeholder'>Тестовая страница (редактирование реализуйте отдельно)</div>`;
                    break;
                case 'file':
                    contentHtml = `
                        <div class="mb-3">
                            <label class="form-label">Файл</label>
                            <input type="file" class="form-control" name="pages[${pageIndex}][content][file]">
                        </div>
                    `;
                    break;
                case 'presentation':
                    contentHtml = `
                        <div class="mb-3">
                            <label class="form-label">URL презентации</label>
                            <input type="url" class="form-control" name="pages[${pageIndex}][content][url]">
                        </div>
                    `;
                    break;
            }
            contentContainer.innerHTML = contentHtml;
        }
    });

    // Обновление номеров страниц
    function updatePageNumbers() {
        const pageItems = pagesContainer.querySelectorAll('.page-item');
        pageItems.forEach((item, index) => {
            item.querySelector('h6').textContent = `Страница ${index + 1}`;
            const inputs = item.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/pages\[\d+\]/, `pages[${index}]`));
                }
            });
        });
    }

    // Инициализация редакторов кода для всех editor-three-panel
    document.querySelectorAll('.editor-three-panel').forEach(function(panel) {
        // HTML
        const htmlTextarea = panel.querySelector('.codemirror-editor-html');
        const htmlEditorDiv = panel.querySelector('.html-editor');
        let htmlEditor = CodeMirror(htmlEditorDiv, {
            value: htmlTextarea.value,
            mode: 'htmlmixed',
            theme: 'material',
            lineNumbers: true,
            lineWrapping: true,
            tabSize: 4,
            indentUnit: 4,
            viewportMargin: Infinity,
        });
        htmlEditor.on('change', function(cm) {
            htmlTextarea.value = cm.getValue();
            updatePreview();
        });

        // CSS
        const cssTextarea = panel.querySelector('.codemirror-editor-css');
        const cssEditorDiv = panel.querySelector('.css-editor');
        let cssEditor = CodeMirror(cssEditorDiv, {
            value: cssTextarea.value,
            mode: 'css',
            theme: 'material',
            lineNumbers: true,
            lineWrapping: true,
            tabSize: 4,
            indentUnit: 4,
            viewportMargin: Infinity,
        });
        cssEditor.on('change', function(cm) {
            cssTextarea.value = cm.getValue();
            updatePreview();
        });

        // Preview
        const previewFrame = panel.querySelector('.preview-frame');
        function updatePreview() {
            const html = htmlEditor.getValue();
            const css = cssEditor.getValue();
            const doc = previewFrame.contentDocument || previewFrame.contentWindow.document;
            doc.open();
            doc.write(`<style>${css}</style>${html}`);
            doc.close();
        }
        updatePreview();
    });

    // TinyMCE для всех .tinymce-editor
    if (window.tinymce) {
        tinymce.init({
            selector: '.tinymce-editor',
            menubar: false,
            plugins: 'lists link image code',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link image | code',
            height: 220,
            branding: false,
            language: 'ru',
        });
    }
});
</script>
@endsection 