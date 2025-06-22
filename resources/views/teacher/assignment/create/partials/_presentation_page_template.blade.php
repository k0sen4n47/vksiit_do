<template id="presentationPageTemplate">
    <div class="assignment-create__page" data-page-type="4">
        <div class="assignment-create__page-header">
            <h3><i class="fas fa-presentation"></i> Страница [INDEX]</h3>
            <button type="button" class="assignment-create__remove-page">
                <i class="fas fa-times"></i>
                Удалить страницу
            </button>
        </div>
        <div class="assignment-create__page-content">
            <div class="assignment-create__form-group">
                <label for="pages[INDEX][content][title]">Заголовок презентации</label>
                <input type="text" name="pages[INDEX][content][title]" id="pages[INDEX][content][title]" required>
            </div>
            <div class="assignment-create__form-group">
                <label for="pages[INDEX][content][description]">Описание презентации</label>
                <textarea name="pages[INDEX][content][description]" id="pages[INDEX][content][description]" rows="3" required></textarea>
            </div>
            <div class="assignment-create__form-group">
                <label for="pages[INDEX][content][presentation]">Презентация</label>
                <input type="file" name="pages[INDEX][content][presentation]" id="pages[INDEX][content][presentation]" accept=".ppt,.pptx,.pdf">
            </div>
        </div>
    </div>
</template> 