@props(['pageIndex', 'questionIndex'])

<div class="test-question">
    <div class="test-question__header">
        <h5>Вопрос <span class="question-number">{{ $questionIndex + 1 }}</span></h5>
        <button type="button" class="btn remove-question">
            <i class="fas fa-times"></i>
            Удалить вопрос
        </button>
    </div>
    <div class="test-question__content">
        <div class="assignment-create__form-group form-group">
            <label class="assignment-create__label">Текст вопроса</label>
            <input type="text" class="assignment-create__input question-text" 
                   name="pages[{{ $pageIndex }}][questions][{{ $questionIndex }}][text]" required>
        </div>

        <div class="assignment-create__form-group form-group">
            <label class="assignment-create__label">Изображение к вопросу</label>
            <input type="file" class="assignment-create__input question-image" 
                   name="pages[{{ $pageIndex }}][questions][{{ $questionIndex }}][image]" 
                   accept="image/*">
            <div class="question-image-preview" style="display: none; margin-top: 10px;">
                <img src="" alt="Предпросмотр" style="max-width: 300px;">
            </div>
        </div>

        <div class="assignment-create__form-group form-group">
            <label class="assignment-create__label">Тип вопроса</label>
            <select class="assignment-create__input question-type" 
                    name="pages[{{ $pageIndex }}][questions][{{ $questionIndex }}][type]" required>
                <option value="single">Один правильный ответ</option>
                <option value="multiple">Несколько правильных ответов</option>
                <option value="text">Текстовый ответ</option>
            </select>
        </div>

        <div class="assignment-create__form-group form-group">
            <label class="assignment-create__label">Баллы</label>
            <input type="number" class="assignment-create__input question-score" 
                   name="pages[{{ $pageIndex }}][questions][{{ $questionIndex }}][score]" 
                   min="1" value="1" required>
        </div>

        <div class="test-answers__container" style="display: none;">
            <!-- Здесь будут ответы -->
        </div>

        <div class="text-answer-container" style="display: none;">
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Правильный ответ</label>
                <input type="text" class="assignment-create__input correct-text-answer" 
                       name="pages[{{ $pageIndex }}][questions][{{ $questionIndex }}][correct_text_answer]">
            </div>
        </div>

        <button type="button" class="btn btn-primary add-answer" 
                data-page-index="{{ $pageIndex }}" 
                data-question-index="{{ $questionIndex }}">
            <i class="fas fa-plus"></i> Добавить ответ
        </button>
    </div>
</div> 