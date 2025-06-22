export function createCodePage(pageIndex) {
    console.log('Creating code page with index:', pageIndex);
    
    // Создаем HTML шаблон статически
    const html = `
        <div class="assignment-create__page" data-page-index="${pageIndex}" data-page-type="code">
            <div class="assignment-create__page-header">
                <h3 class="assignment-create__page-title">Страница ${pageIndex + 1}</h3>
                <button type="button" class="assignment-create__page-remove" data-page-index="${pageIndex}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="assignment-create__page-body">
                <div class="assignment-create__form-group form-group">
                    <label for="page_title_${pageIndex}" class="assignment-create__label">Заголовок страницы</label>
                    <input type="text" id="page_title_${pageIndex}" name="pages[${pageIndex}][title]" class="assignment-create__input" required>
                </div>
                <div class="assignment-create__form-group form-group">
                    <label for="page_description_${pageIndex}" class="assignment-create__label">Описание задания</label>
                    <textarea id="page_description_${pageIndex}" name="pages[${pageIndex}][description]" class="assignment-create__textarea" rows="3"></textarea>
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
                                <textarea name="pages[${pageIndex}][html]" style="display: none;"></textarea>
                            </div>
                            <div class="assignment-create__form-group form-group">
                                <label class="assignment-create__label">CSS</label>
                                <div class="css-editor"></div>
                                <textarea name="pages[${pageIndex}][css]" style="display: none;"></textarea>
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
    `;
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;
    const pageElement = tempDiv.firstElementChild;
    
    console.log('Created page element:', pageElement);
    
    return Promise.resolve(pageElement);
} 