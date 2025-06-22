@props(['pageIndex', 'questionIndex', 'answerIndex'])

<div class="test-answer">
    <div class="test-answer__header">
        <h5>Ответ {{ $answerIndex + 1 }}</h5>
        <button type="button" class="btn remove-answer">
            <i class="fas fa-times"></i>
            Удалить ответ
        </button>
    </div>
    <div class="test-answer__content">
        <div class="assignment-create__form-group form-group">
            <label class="assignment-create__label">Текст ответа</label>
            <input type="text" class="assignment-create__input answer-text" 
                   name="pages[{{ $pageIndex }}][questions][{{ $questionIndex }}][answers][{{ $answerIndex }}][text]" required>
        </div>
        <div class="assignment-create__form-group form-group">
            <label class="assignment-create__label">Правильный ответ</label>
            <input type="radio" class="answer-correct" 
                   name="pages[{{ $pageIndex }}][questions][{{ $questionIndex }}][correct_answer]" 
                   value="{{ $answerIndex }}">
        </div>
    </div>
</div> 