export function initCodeMirror(pageElement) {
    console.log('Initializing CodeMirror for page:', pageElement);
    console.log('Page element HTML:', pageElement.outerHTML);
    
    // Находим контейнеры для редакторов
    const htmlEditorContainer = pageElement.querySelector('.html-editor');
    const cssEditorContainer = pageElement.querySelector('.css-editor');
    const previewFrame = pageElement.querySelector('.preview-frame');
    
    // Находим скрытые textarea поля
    const htmlTextarea = pageElement.querySelector('textarea[name*="[html]"]');
    const cssTextarea = pageElement.querySelector('textarea[name*="[css]"]');
    
    console.log('Found elements:', {
        htmlEditor: htmlEditorContainer,
        cssEditor: cssEditorContainer,
        preview: previewFrame,
        htmlTextarea: htmlTextarea,
        cssTextarea: cssTextarea
    });
    
    if (!htmlEditorContainer || !cssEditorContainer || !previewFrame) {
        console.error('Required elements not found:', {
            htmlEditor: !!htmlEditorContainer,
            cssEditor: !!cssEditorContainer,
            preview: !!previewFrame
        });
        return;
    }

    if (!htmlTextarea || !cssTextarea) {
        console.error('Hidden textarea fields not found:', {
            htmlTextarea: !!htmlTextarea,
            cssTextarea: !!cssTextarea
        });
        return;
    }

    // Инициализируем HTML редактор
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
        value: htmlTextarea.value || '',
        extraKeys: {
            'Ctrl-Space': 'autocomplete'
        }
    });

    // Инициализируем CSS редактор
    const cssEditor = CodeMirror(cssEditorContainer, {
        mode: 'css',
        theme: 'default',
        lineNumbers: true,
        autoCloseBrackets: true,
        matchBrackets: true,
        indentUnit: 4,
        tabSize: 4,
        lineWrapping: true,
        value: cssTextarea.value || '',
        extraKeys: {
            'Ctrl-Space': 'autocomplete'
        }
    });

    // Функция сохранения значений в textarea
    function saveToTextarea() {
        htmlTextarea.value = htmlEditor.getValue();
        cssTextarea.value = cssEditor.getValue();
        console.log('Saved to textarea:', {
            html: htmlTextarea.value,
            css: cssTextarea.value
        });
    }

    // Функция обновления превью
    function updatePreview() {
        const html = htmlEditor.getValue();
        const css = cssEditor.getValue();
        const previewDocument = previewFrame.contentDocument || previewFrame.contentWindow.document;
        
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
        
        // Сохраняем в textarea при каждом изменении
        saveToTextarea();
    }

    // Добавляем обработчики изменений
    htmlEditor.on('change', function(cm) {
        updatePreview();
        console.log('HTML changed:', cm.getValue());
    });
    
    cssEditor.on('change', function(cm) {
        updatePreview();
        console.log('CSS changed:', cm.getValue());
    });

    // Инициализируем превью
    updatePreview();
    
    // Принудительно сохраняем начальные значения в textarea
    saveToTextarea();

    // Добавляем обработчик изменения темы
    const themeSelect = pageElement.querySelector('.theme-selector');
    if (themeSelect) {
        themeSelect.addEventListener('change', (e) => {
            const theme = e.target.value;
            htmlEditor.setOption('theme', theme);
            cssEditor.setOption('theme', theme);
        });
    }

    // Добавляем обработчик для кнопки скачивания
    const downloadButton = pageElement.querySelector('.download-zip');
    if (downloadButton) {
        downloadButton.addEventListener('click', () => {
            const html = htmlEditor.getValue();
            const css = cssEditor.getValue();
            
            // Создаем ZIP архив
            const zip = new JSZip();
            zip.file('index.html', html);
            zip.file('styles.css', css);
            
            // Генерируем и скачиваем архив
            zip.generateAsync({ type: 'blob' })
                .then(content => {
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(content);
                    link.download = 'code.zip';
                    link.click();
                });
        });
    }

    console.log('CodeMirror initialized successfully');
} 