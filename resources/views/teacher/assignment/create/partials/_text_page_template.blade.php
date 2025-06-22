<template id="textPageTemplate">
    <div class="assignment-create__page" data-page-type="text">
        <div class="assignment-create__page-header">
            <h3 class="assignment-create__page-title">Текстовая страница</h3>
            <button type="button" class="assignment-create__page-remove">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="assignment-create__page-body">
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Заголовок</label>
                <input type="text" class="assignment-create__input" name="pages[PAGE_INDEX][title]" required>
            </div>
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Описание</label>
                <textarea id="tinymce-editor-page-[PAGE_INDEX]" class="assignment-create__textarea tinymce-editor" name="pages[PAGE_INDEX][content]" rows="3"></textarea>
            </div>
        </div>
    </div>
</template>