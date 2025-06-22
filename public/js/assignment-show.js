// assignment-show.js
// Здесь можно добавить интерактив для страницы просмотра задания 

// Инициализация редакторов кода на странице просмотра задания
document.addEventListener('DOMContentLoaded', function() {
    console.log('assignment-show.js loaded');
    
    // Инициализация редакторов кода
    function initCodeEditors() {
        const codePages = document.querySelectorAll('.assignment-create__page[data-page-type="code"]');
        
        codePages.forEach(function(page) {
            const htmlEditorDiv = page.querySelector('.html-editor');
            const cssEditorDiv = page.querySelector('.css-editor');
            const htmlTextarea = page.querySelector('.html-textarea');
            const cssTextarea = page.querySelector('.css-textarea');
            const previewFrame = page.querySelector('.preview-frame');
            const themeSelector = page.querySelector('.theme-selector');
            
            if (!htmlEditorDiv || !cssEditorDiv) return;

            // Удаляем старые редакторы, если есть
            htmlEditorDiv.innerHTML = '';
            cssEditorDiv.innerHTML = '';

            // Инициализация CodeMirror
            const htmlEditor = CodeMirror(htmlEditorDiv, {
                mode: 'htmlmixed',
                lineNumbers: true,
                autoCloseTags: true,
                theme: themeSelector ? themeSelector.value : 'default',
                value: htmlTextarea ? htmlTextarea.value || '' : '',
                readOnly: true, // Только для просмотра
            });
            
            const cssEditor = CodeMirror(cssEditorDiv, {
                mode: 'css',
                lineNumbers: true,
                autoCloseBrackets: true,
                theme: themeSelector ? themeSelector.value : 'default',
                value: cssTextarea ? cssTextarea.value || '' : '',
                readOnly: true, // Только для просмотра
            });

            // Смена темы
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
        });
    }

    // Инициализируем редакторы при загрузке страницы
    initCodeEditors();
}); 