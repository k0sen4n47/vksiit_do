<!-- Шаблон для вопроса -->
<template id="question-template">
    <div class="test-question">
        <div class="test-question__header">
            <h5>Вопрос <span class="question-number"></span></h5>
            <button type="button" class="btn remove-question">
                <i class="fas fa-times"></i>
                Удалить вопрос
            </button>
        </div>
        <div class="test-question__content">
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Текст вопроса</label>
                <input type="text" class="assignment-create__input question-text" required>
            </div>
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Тип вопроса</label>
                <select class="assignment-create__input question-type" required>
                    <option value="single">Один правильный ответ</option>
                    <option value="multiple">Несколько правильных ответов</option>
                    <option value="text">Текстовый ответ</option>
                </select>
            </div>
            <div class="assignment-create__form-group form-group">
                <label class="assignment-create__label">Баллы</label>
                <input type="number" class="assignment-create__input question-score" min="1" value="1" required>
            </div>
            <div class="test-answers">
                <div class="test-answers__header">
                    <h6>Ответы</h6>
                    <button type="button" class="btn btn-primary btn-sm add-answer">
                        <i class="fas fa-plus"></i> Добавить ответ
                    </button>
                </div>
                <div class="test-answers__container">
                    <!-- Здесь будут ответы -->
                </div>
            </div>
        </div>
    </div>
</template>

<!-- Шаблон для ответа -->
<template id="answer-template">
    <div class="test-answer">
        <div class="test-answer__content">
            <div class="assignment-create__form-group form-group">
                <div class="test-answer__controls">
                    <div class="form-check">
                        <input type="radio" class="form-check-input answer-correct" value="1">
                        <label class="form-check-label">Правильный ответ</label>
                    </div>
                    <button type="button" class="btn remove-question">
                        <i class="fas fa-times"></i>
                        Удалить ответ
                    </button>
                </div>
                <input type="text" class="assignment-create__input answer-text" placeholder="Введите текст ответа" required>
            </div>
        </div>
    </div>
</template> 