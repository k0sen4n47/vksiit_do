@extends('layouts.app')

@section('content')
<div class="editor-container">
    <div class="editor-header">
        <h1>Редактор кода</h1>
        <div class="editor-controls">
            <div class="theme-selector">
                <select id="theme-selector" aria-label="Выберите тему редактора">
                    <option value="dracula">Dracula</option>
                    <option value="monokai">Monokai</option>
                    <option value="material">Material</option>
                    <option value="solarized dark">Solarized Dark</option>
                    <option value="solarized light">Solarized Light</option>
                    <option value="eclipse">Eclipse</option>
                    <option value="default">Default</option>
                </select>
            </div>
            <button id="save-code" class="btn btn-primary">Сохранить</button>
            <button id="download-zip" class="btn btn-secondary">Сохранить как архив</button>
        </div>
    </div>
    
    <div class="editor-main">
        <div class="editor-panels">
            <div class="editor-panel">
                <div class="editor-panel-header">
                    <h3>HTML</h3>
                </div>
                <textarea id="html-editor"></textarea>
            </div>
            
            <div class="editor-panel">
                <div class="editor-panel-header">
                    <h3>CSS</h3>
                </div>
                <textarea id="css-editor"></textarea>
            </div>
        </div>
        
        <div class="preview-panel">
            <div class="preview-header">
                <h3>Предпросмотр</h3>
            </div>
            <iframe id="preview-frame"></iframe>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/material.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/solarized.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/eclipse.min.css">
@vite(['resources/css/pages/editor.css'])
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
<!-- JSZip library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
@vite(['resources/js/editor.js'])
@endpush 