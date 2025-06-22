<template id="filePageTemplate">
    <div class="assignment-create__page" data-page-type="3">
        <div class="assignment-create__page-header">
            <h3><i class="fas fa-file-upload"></i> Страница [INDEX]</h3>
            <button type="button" class="assignment-create__remove-page">
                <i class="fas fa-times"></i>
                Удалить страницу
            </button>
        </div>
        <div class="assignment-create__page-bodyt">
            <div class="assignment-create__form-group">
                <label for="pages[INDEX][content][title]">Заголовок задания</label>
                <input type="text" name="pages[INDEX][content][title]" id="pages[INDEX][content][title]" required>
            </div>
            <div class="assignment-create__form-group">
                <label for="pages[INDEX][content][description]">Описание задания</label>
                <textarea name="pages[INDEX][content][description]" id="pages[INDEX][content][description]" rows="3" required></textarea>
            </div>
            <div class="assignment-create__form-group">
                <label for="pages[INDEX][content][files]">Файлы</label>
                <input type="file" name="pages[INDEX][content][files][]" id="pages[INDEX][content][files]" multiple>
            </div>
        </div>
    </div>
</template> 