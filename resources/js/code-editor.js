// Инициализация редакторов
let editors = new Map();

// Функция инициализации редакторов для страницы
function initEditorsForPage(pageElement) {
    const pageIndex = pageElement.dataset.pageIndex;
    const htmlEditor = CodeMirror.fromTextArea(pageElement.querySelector('.html-editor'), {
        mode: 'htmlmixed',
        theme: 'dracula',
        lineNumbers: true,
        autoCloseTags: true,
        autoCloseBrackets: true,
        matchBrackets: true,
        indentUnit: 4,
        tabSize: 4,
        lineWrapping: true,
        foldGutter: true,
        gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
        extraKeys: {
            'Ctrl-Space': 'autocomplete',
            'Ctrl-F': 'findPersistent',
            'Ctrl-/': 'toggleComment',
            'F11': function(cm) {
                cm.setOption('fullScreen', !cm.getOption('fullScreen'));
            },
            'Esc': function(cm) {
                if (cm.getOption('fullScreen')) cm.setOption('fullScreen', false);
            }
        }
    });

    const cssEditor = CodeMirror.fromTextArea(pageElement.querySelector('.css-editor'), {
        mode: 'css',
        theme: 'dracula',
        lineNumbers: true,
        autoCloseBrackets: true,
        matchBrackets: true,
        indentUnit: 4,
        tabSize: 4,
        lineWrapping: true,
        foldGutter: true,
        gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
        extraKeys: {
            'Ctrl-Space': 'autocomplete',
            'Ctrl-F': 'findPersistent',
            'Ctrl-/': 'toggleComment',
            'F11': function(cm) {
                cm.setOption('fullScreen', !cm.getOption('fullScreen'));
            },
            'Esc': function(cm) {
                if (cm.getOption('fullScreen')) cm.setOption('fullScreen', false);
            }
        }
    });

    // Сохраняем редакторы в Map
    editors.set(pageIndex, {
        html: htmlEditor,
        css: cssEditor,
        preview: pageElement.querySelector('.preview-frame')
    });

    // Обновление предпросмотра при изменении кода
    htmlEditor.on('change', () => updatePreview(pageIndex));
    cssEditor.on('change', () => updatePreview(pageIndex));

    // Обработчики для селекторов
    const themeSelector = pageElement.querySelector('.theme-selector');
    themeSelector.addEventListener('change', function() {
        changeTheme(pageIndex, this.value);
    });

    const languageSelector = pageElement.querySelector('.language-selector');
    languageSelector.addEventListener('change', function() {
        changeLanguage(pageIndex, this.value);
    });

    // Обработчики для кнопок
    const saveButton = pageElement.querySelector('.save-code');
    saveButton.addEventListener('click', () => saveCode(pageIndex));

    const downloadButton = pageElement.querySelector('.download-zip');
    downloadButton.addEventListener('click', () => downloadAsZip(pageIndex));

    // Первоначальное обновление предпросмотра
    updatePreview(pageIndex);
}

// Функция обновления предпросмотра
function updatePreview(pageIndex) {
    const editor = editors.get(pageIndex);
    if (!editor) return;

    const previewDocument = editor.preview.contentDocument || editor.preview.contentWindow.document;
    const html = editor.html.getValue();
    const css = editor.css.getValue();
    
    previewDocument.open();
    previewDocument.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <style>${css}</style>
        </head>
        <body>${html}</body>
        </html>
    `);
    previewDocument.close();
}

// Функция изменения темы
function changeTheme(pageIndex, theme) {
    const editor = editors.get(pageIndex);
    if (!editor) return;

    editor.html.setOption('theme', theme);
    editor.css.setOption('theme', theme);
}

// Функция изменения языка
function changeLanguage(pageIndex, language) {
    const editor = editors.get(pageIndex);
    if (!editor) return;

    editor.html.setOption('mode', language);
    editor.css.setOption('mode', language);
}

// Функция сохранения кода
function saveCode(pageIndex) {
    const editor = editors.get(pageIndex);
    if (!editor) return;

    const html = editor.html.getValue();
    const css = editor.css.getValue();
    
    // Здесь можно добавить логику сохранения кода
    console.log('HTML:', html);
    console.log('CSS:', css);
}

// Функция скачивания кода как ZIP
async function downloadAsZip(pageIndex) {
    const editor = editors.get(pageIndex);
    if (!editor) return;

    const zip = new JSZip();
    
    // Добавляем файлы в архив
    zip.file('index.html', editor.html.getValue());
    zip.file('styles.css', editor.css.getValue());
    
    // Генерируем и скачиваем архив
    const content = await zip.generateAsync({type: 'blob'});
    const link = document.createElement('a');
    link.href = URL.createObjectURL(content);
    link.download = 'code.zip';
    link.click();
}

// Инициализация TinyMCE для текстовых редакторов
function initTinyMCE() {
    tinymce.init({
        selector: '.tinymce-editor',
        height: 300,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
    });
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация TinyMCE
    initTinyMCE();
    
    // Обработчик для добавления новой страницы
    document.addEventListener('pageAdded', function(e) {
        const pageElement = e.detail.pageElement;
        initEditorsForPage(pageElement);
    });
}); 