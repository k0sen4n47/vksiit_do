export function createTextPage(pageIndex) {
    const pageElement = document.createElement('div');
    pageElement.className = 'assignment-create__page';
    pageElement.setAttribute('data-page-index', pageIndex);
    pageElement.setAttribute('data-page-type', 'text');

    const editorId = `tinymce-editor-page-${pageIndex}`;

    pageElement.innerHTML = `
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
                <label for="${editorId}" class="assignment-create__label">Содержимое страницы</label>
                <textarea id="${editorId}" name="pages[${pageIndex}][content]" class="assignment-create__textarea tinymce-editor" rows="10"></textarea>
            </div>
        </div>
    `;

    return pageElement;
}
