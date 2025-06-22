@extends('layouts.app')

@section('title-page')
{{ $assignment->title }}
@endsection

@section('content')
<div class="student-assignment-view" id="student-assignment-view" data-assignment-id="{{ $assignment->id }}">
    <h1 class="student-assignment-view__title">{{ $assignment->title }}</h1>
    <div class="student-assignment-view__description">{{ $assignment->description }}</div>
    @php $pagesCount = $assignment->pages->count(); @endphp
    @if($pagesCount > 1)
        <div class="student-assignment-view__progress">
            <span id="page-indicator">Страница <span id="current-page">1</span> из <span id="total-pages">{{ $pagesCount }}</span></span>
            <div class="student-assignment-view__progress-bar">
                <div class="student-assignment-view__progress-bar-inner" id="progress-bar-inner"></div>
            </div>
        </div>
    @endif
    <div class="student-assignment-view__pages" id="assignment-pages" data-pages-count="{{ $assignment->pages->count() }}">
        @foreach($assignment->pages as $i => $page)
            <div class="student-assignment-view__page" data-page-index="{{ $i }}" data-page-type="{{ $page->type }}" style="display: {{ $i === 0 ? 'block' : 'none' }};">
                <h3>{{ $page->content['title'] ?? 'Страница' }}</h3>
                @if($page->type === 'text')
                    <div class="student-assignment-view__text-content">
                        <div class="student-assignment-view__page-content">{!! $page->content['text'] ?? '' !!}</div>
                    </div>
                @elseif($page->type === 'code')
                    @php
                        $html = '';
                        $css = '';
                        $title = '';
                        $description = '';
                        // Если content - строка (json), декодируем
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
                    <div class="student-assignment-view__code-content">
                        @if($title)
                            <h4 class="student-assignment-view__code-title">{{ $title }}</h4>
                        @endif
                        @if($description)
                            <div class="student-assignment-view__code-description">{{ $description }}</div>
                        @endif
                        
                        <!-- Скрытые поля с кодом преподавателя для инициализации редактора -->
                        <textarea class="html-textarea" style="display: none;">{{ $html }}</textarea>
                        <textarea class="css-textarea" style="display: none;">{{ $css }}</textarea>
                        
                        <div class="student-assignment-view__code-info">
                            <h5>
                                <i class="fas fa-info-circle"></i>
                                Задание с кодом
                            </h5>
                            <p>
                                Ниже вы найдете редактор кода с начальным кодом от преподавателя. 
                                Вы можете изменять и дополнять этот код, а затем отправить его как ответ на задание.
                            </p>
                        </div>
                    </div>
                    <div class="assignment-create__edit-code">
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
                    </div>
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
                                    <iframe class="preview-frame" style="height: 300px; border: 1px solid #ddd;"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($page->type === 'test')
                    <div class="student-assignment-view__test-content">
                        @if($page->test && $page->test->questions->count())
                            <form class="student-assignment-view__test-form">
                                @foreach($page->test->questions as $qIndex => $question)
                                    <div class="test-question">
                                        <div class="test-question__header">
                                            <span class="test-question__number">Вопрос {{ $qIndex + 1 }}</span>
                                            <h4 class="test-question__title">{{ $question->text }}</h4>
                                        </div>
                                        <div class="test-question__content">
                                            @if($question->type === 'text')
                                                <div class="test-question__text-answer">
                                                    <textarea name="test_answer_{{ $question->id }}" class="student-assignment-view__textarea" placeholder="Введите ваш ответ..."></textarea>
                                                </div>
                                            @elseif($question->type === 'checkbox')
                                                <div class="test-question__options">
                                                    @foreach($question->answers as $answer)
                                                        <div class="test-question__option">
                                                            <input type="checkbox" name="test_answer_{{ $question->id }}[]" value="{{ $answer->id }}" class="test-question__input" id="q{{ $question->id }}a{{ $answer->id }}">
                                                            <label class="test-question__label" for="q{{ $question->id }}a{{ $answer->id }}">{{ $answer->text }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="test-question__options">
                                                    @foreach($question->answers as $answer)
                                                        <div class="test-question__option">
                                                            <input type="radio" name="test_answer_{{ $question->id }}" value="{{ $answer->id }}" class="test-question__input" id="q{{ $question->id }}a{{ $answer->id }}">
                                                            <label class="test-question__label" for="q{{ $question->id }}a{{ $answer->id }}">{{ $answer->text }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </form>
                        @else
                            <div class="student-assignment-view__test-empty">
                                Тест не найден или не содержит вопросов.
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    <div class="student-assignment-view__nav">
        @if($pagesCount > 1)
            <button type="button" class="btn-cabinet" id="prev-page">← Назад</button>
            <button type="button" class="btn-cabinet" id="next-page">Вперёд →</button>
        @endif
    </div>
</div>

@if(!$studentAnswer)
<div class="student-assignment-view__final-answer" style="margin-top: 40px; padding: 24px; border: 1px solid #e0e0e0; border-radius: 8px; background: #fafbfc;" id="answer-form-block">
    <h3 style="margin-bottom: 16px;">Ваш ответ на задание</h3>
    
    @if($assignment->pages->where('type', 'code')->count() > 0)
        <!-- Для заданий с кодом - только прикрепление файлов -->
        <div class="student-assignment-view__code-info">
            <h5>
                <i class="fas fa-info-circle"></i>
                Задание с кодом
            </h5>
            <p>
                Работайте с кодом в редакторе выше. Здесь вы можете прикрепить дополнительные файлы к вашему ответу.
            </p>
        </div>
        
        <form id="finalAnswerForm" method="POST" action="{{ route('student.assignments.answer', $assignment->id) }}" enctype="multipart/form-data" style="margin-top: 20px;">
            @csrf
            <input type="hidden" name="page_id" value="{{ $assignment->pages->first()->id }}">
            
            <!-- Скрытые поля для кода из редактора -->
            <textarea name="answer_html" class="html-textarea-student" style="display: none;"></textarea>
            <textarea name="answer_css" class="css-textarea-student" style="display: none;"></textarea>
            
            <div class="answer-content">
                <label style="font-weight: 500; margin-bottom: 6px; display: block;">Прикрепить файлы:</label>
                <input type="file" name="files[]" multiple>
                <p style="font-size: 0.9em; color: #6c757d; margin-top: 8px;">Вы можете прикрепить один или несколько файлов к вашему ответу.</p>
            </div>
            
            <button type="submit" class="btn-cabinet" style="font-size:1.1em; margin-top: 20px;">Отправить ответ</button>
        </form>
    @else
        <!-- Для обычных заданий - вкладки с текстом и файлами -->
        <div class="answer-tabs">
            <div class="answer-tab active" data-tab="text">
                <i class="fas fa-font"></i> Текстовый ответ
            </div>
            <div class="answer-tab" data-tab="file">
                <i class="fas fa-paperclip"></i> Загрузить файл
            </div>
        </div>
        <form id="finalAnswerForm" method="POST" action="{{ route('student.assignments.answer', $assignment->id) }}" enctype="multipart/form-data" style="margin-top: 20px;">
            @csrf
            <input type="hidden" name="page_id" value="{{ $assignment->pages->first()->id }}">
            <div class="answer-content" id="answer-content-text">
                <label style="font-weight: 500; margin-bottom: 6px; display: block;">Текстовый ответ:</label>
                <textarea id="final_answer_text" name="answer_text" class="student-assignment-view__textarea tinymce-editor" placeholder="Введите ваш ответ..." rows="10"></textarea>
            </div>
            <div class="answer-content" id="answer-content-file" style="display: none;">
                <label style="font-weight: 500; margin-bottom: 6px; display: block;">Прикрепить файлы:</label>
                <input type="file" name="files[]" multiple>
                <p style="font-size: 0.9em; color: #6c757d; margin-top: 8px;">Вы можете прикрепить один или несколько файлов.</p>
            </div>
            <button type="submit" class="btn-cabinet" style="font-size:1.1em; margin-top: 20px;">Отправить ответ</button>
        </form>
    @endif
</div>
@else
<div class="student-assignment-view__final-answer" id="answer-view-block" style="margin-top: 40px; padding: 24px; border: 1px solid #e0e0e0; border-radius: 8px; background: #fafbfc;">
    <h3 style="margin-bottom: 16px;">Ваш отправленный ответ</h3>
    @if($studentAnswer->answer_text)
        <div style="margin-bottom: 16px;">{!! $studentAnswer->answer_text !!}</div>
    @endif
    @if($studentAnswer->answer_html || $studentAnswer->answer_css)
        <div style="margin-bottom: 16px;">
            <strong>Ваш код:</strong>
            @if($studentAnswer->answer_html)
                <div style="margin-top: 8px;">
                    <strong>HTML:</strong>
                    <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto;"><code>{{ $studentAnswer->answer_html }}</code></pre>
                </div>
            @endif
            @if($studentAnswer->answer_css)
                <div style="margin-top: 8px;">
                    <strong>CSS:</strong>
                    <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto;"><code>{{ $studentAnswer->answer_css }}</code></pre>
                </div>
            @endif
        </div>
    @endif
    @if($studentAnswer->files)
        @php
            $files = is_array($studentAnswer->files) ? $studentAnswer->files : (is_string($studentAnswer->files) ? json_decode($studentAnswer->files, true) : []);
        @endphp
        @if($files && count($files))
            <div style="margin-bottom: 16px;">
                <strong>Файлы:</strong>
                <ul>
                    @foreach($files as $file)
                        <li><a href="{{ asset('storage/' . $file) }}" target="_blank">{{ basename($file) }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
    <div style="color: #28a745; font-weight: 500; margin-bottom: 16px;">Ответ отправлен.</div>
    <button type="button" class="btn-cabinet" id="edit-answer-btn">Редактировать</button>
</div>
<div class="student-assignment-view__final-answer" id="answer-edit-block" style="display:none; margin-top: 40px; padding: 24px; border: 1px solid #e0e0e0; border-radius: 8px; background: #fafbfc;">
    <h3 style="margin-bottom: 16px;">Редактировать ответ</h3>
    <div class="answer-tabs">
        <div class="answer-tab active" data-tab="text">
            <i class="fas fa-font"></i> Текстовый ответ
        </div>
        <div class="answer-tab" data-tab="file">
            <i class="fas fa-paperclip"></i> Загрузить файл
        </div>
    </div>
    <form id="editAnswerForm" method="POST" action="{{ route('student.assignments.answer', $assignment->id) }}" enctype="multipart/form-data" style="margin-top: 20px;">
        @csrf
        <input type="hidden" name="page_id" value="{{ $assignment->pages->first()->id }}">
        <div class="answer-content" id="edit-answer-content-text">
            <label style="font-weight: 500; margin-bottom: 6px; display: block;">Текстовый ответ:</label>
            <textarea id="edit_answer_text" name="answer_text" class="student-assignment-view__textarea tinymce-editor" placeholder="Введите ваш ответ..." rows="10">{{ $studentAnswer->answer_text }}</textarea>
        </div>
        <div class="answer-content" id="edit-answer-content-file" style="display: none;">
            <label style="font-weight: 500; margin-bottom: 6px; display: block;">Прикрепить файлы:</label>
            <input type="file" name="files[]" multiple>
            <p style="font-size: 0.9em; color: #6c757d; margin-top: 8px;">Вы можете прикрепить один или несколько файлов. Старые файлы останутся доступны.</p>
        </div>
        <button type="submit" class="btn-cabinet" style="font-size:1.1em; margin-top: 20px;">Сохранить изменения</button>
        <button type="button" class="btn-cabinet" id="cancel-edit-btn" style="margin-left: 10px; background: #ccc; color: #222;">Отмена</button>
    </form>
</div>
@endif

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/material.min.css">
    
    <style>
        .answer-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .answer-tab {
            padding: 10px 20px;
            cursor: pointer;
            font-weight: 500;
            color: #495057;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            transition: all 0.2s ease-in-out;
        }
        .answer-tab:hover {
            background-color: #e9ecef;
            border-color: #ced4da;
        }
        .answer-tab.active {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .answer-tab i {
            margin-right: 8px;
        }
        
        /* Стили для редактора кода студента */
        .student-code-editor {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .student-code-editor__tabs {
            display: flex;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        
        .student-code-editor__tab {
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 500;
            color: #495057;
            background-color: transparent;
            border: none;
            transition: all 0.2s ease-in-out;
        }
        
        .student-code-editor__tab:hover {
            background-color: #e9ecef;
        }
        
        .student-code-editor__tab.active {
            color: #007bff;
            background-color: #fff;
            border-bottom: 2px solid #007bff;
        }
        
        .student-code-editor__content {
            position: relative;
        }
        
        .student-code-editor__panel {
            display: none;
        }
        
        .student-code-editor__panel.active {
            display: block;
        }
        
        .student-code-editor__preview {
            padding: 16px;
            background-color: #fff;
            border-top: 1px solid #dee2e6;
        }
        
        .html-editor-student,
        .css-editor-student {
            height: 200px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closebrackets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/closetag.min.js"></script>
    <script src="/js/tinymce/js/tinymce/tinymce.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Табы для обычных заданий ---
        const tabs = document.querySelectorAll('.answer-tab');
        const textContent = document.getElementById('answer-content-text');
        const fileContent = document.getElementById('answer-content-file');
        const fileInput = document.querySelector('input[name="files[]"]');
        let tinymceInitialized = false;
        let tinymceEditor = null;
        let mainCodeEditors = null;

        // Обработка переключения вкладок (только для обычных заданий)
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabType = this.dataset.tab;
                
                // Убираем активный класс со всех вкладок
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Скрываем все контенты
                [textContent, fileContent].forEach(content => {
                    if (content) content.style.display = 'none';
                });
                
                // Показываем нужный контент
                switch(tabType) {
                    case 'text':
                        if (textContent) textContent.style.display = 'block';
                        break;
                    case 'file':
                        if (fileContent) fileContent.style.display = 'block';
                        break;
                }
            });
        });

        // Инициализация основного редактора кода на странице задания
        function initMainCodeEditor() {
            if (mainCodeEditors) return; // Уже инициализирован
            
            const htmlEditorContainer = document.querySelector('.html-editor');
            const cssEditorContainer = document.querySelector('.css-editor');
            const htmlTextarea = document.querySelector('.html-textarea');
            const cssTextarea = document.querySelector('.css-textarea');
            const previewFrame = document.querySelector('.preview-frame');
            
            if (!htmlEditorContainer || !cssEditorContainer) return;
            
            // --- Логика сохранения/загрузки из localStorage ---
            const assignmentId = document.getElementById('student-assignment-view').dataset.assignmentId;
            const studentId = {{ Auth::id() }};
            const localStorageKey = `code_assignment_${assignmentId}_student_${studentId}`;

            // Получаем код преподавателя из скрытых полей
            let teacherHtml = htmlTextarea ? htmlTextarea.value || '' : '';
            let teacherCss = cssTextarea ? cssTextarea.value || '' : '';

            // Пытаемся загрузить сохраненный код студента
            let savedStudentCode = localStorage.getItem(localStorageKey);
            let initialHtml = teacherHtml;
            let initialCss = teacherCss;

            if (savedStudentCode) {
                try {
                    const parsedCode = JSON.parse(savedStudentCode);
                    if (typeof parsedCode.html === 'string') initialHtml = parsedCode.html;
                    if (typeof parsedCode.css === 'string') initialCss = parsedCode.css;
                } catch (e) {
                    // fallback к teacherHtml/teacherCss
                }
            }

            const htmlEditor = CodeMirror(htmlEditorContainer, {
                mode: 'htmlmixed',
                theme: 'default',
                lineNumbers: true,
                autoCloseTags: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                indentUnit: 4,
                tabSize: 4,
                lineWrapping: true,
                value: initialHtml,
                extraKeys: {
                    'Ctrl-Space': 'autocomplete'
                }
            });
            const cssEditor = CodeMirror(cssEditorContainer, {
                mode: 'css',
                theme: 'default',
                lineNumbers: true,
                autoCloseBrackets: true,
                matchBrackets: true,
                indentUnit: 4,
                tabSize: 4,
                lineWrapping: true,
                value: initialCss,
                extraKeys: {
                    'Ctrl-Space': 'autocomplete'
                }
            });

            // Сохраняем код при каждом изменении
            function saveStudentCode() {
                const studentCode = {
                    html: htmlEditor.getValue(),
                    css: cssEditor.getValue()
                };
                localStorage.setItem(localStorageKey, JSON.stringify(studentCode));
            }
            htmlEditor.on('change', saveStudentCode);
            cssEditor.on('change', saveStudentCode);

            // Функция сохранения в textarea
            function saveToTextarea() {
                if (htmlTextarea) htmlTextarea.value = htmlEditor.getValue();
                if (cssTextarea) cssTextarea.value = cssEditor.getValue();
            }

            // Функция обновления превью
            function updatePreview() {
                if (!previewFrame) return;
                const html = htmlEditor.getValue();
                const css = cssEditor.getValue();
                const doc = previewFrame.contentDocument || previewFrame.contentWindow.document;
                doc.open();
                doc.write(`<!DOCTYPE html><html><head><style>${css}</style></head><body>${html}</body></html>`);
                doc.close();
                saveToTextarea();
            }

            htmlEditor.on('change', updatePreview);
            cssEditor.on('change', updatePreview);
            updatePreview();

            // Обработка вкладок редактора (если есть)
            const editorTabs = document.querySelectorAll('.assignment-create__code-tab');
            const editorPanels = document.querySelectorAll('.assignment-create__code-panel');
            editorTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const editorType = this.dataset.editor;
                    editorTabs.forEach(t => t.classList.remove('active'));
                    editorPanels.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    document.querySelector(`.assignment-create__code-panel[data-editor="${editorType}"]`).classList.add('active');
                });
            });

            // Обработчик смены темы
            const themeSelector = document.querySelector('.theme-selector');
            if (themeSelector) {
                const savedTheme = localStorage.getItem('codeEditorTheme') || 'default';
                themeSelector.value = savedTheme;
                htmlEditor.setOption('theme', savedTheme);
                cssEditor.setOption('theme', savedTheme);
                themeSelector.addEventListener('change', function() {
                    const newTheme = this.value;
                    htmlEditor.setOption('theme', newTheme);
                    cssEditor.setOption('theme', newTheme);
                    localStorage.setItem('codeEditorTheme', newTheme);
                });
            }

            mainCodeEditors = { htmlEditor, cssEditor };
        }
        
        // Обработчик отправки формы для заданий с кодом
        const finalAnswerForm = document.getElementById('finalAnswerForm');
        if (finalAnswerForm) {
            finalAnswerForm.addEventListener('submit', function(e) {
                if (mainCodeEditors) {
                    const htmlTextarea = document.querySelector('.html-textarea-student');
                    const cssTextarea = document.querySelector('.css-textarea-student');
                    if (htmlTextarea) htmlTextarea.value = mainCodeEditors.htmlEditor.getValue();
                    if (cssTextarea) cssTextarea.value = mainCodeEditors.cssEditor.getValue();
                    // Очищаем localStorage перед отправкой
                    const assignmentId = document.getElementById('student-assignment-view').dataset.assignmentId;
                    const studentId = {{ Auth::id() }};
                    const localStorageKey = `code_assignment_${assignmentId}_student_${studentId}`;
                    localStorage.removeItem(localStorageKey);
                }
            });
        }
        
        // Инициализируем основной редактор кода, если есть страница с кодом
        if (document.querySelector('.student-assignment-view__page[data-page-type="code"]')) {
            initMainCodeEditor();
        }
    });
    </script>
    @vite(['resources/js/pages/student-assignment.js'])
@endpush

@endsection 
