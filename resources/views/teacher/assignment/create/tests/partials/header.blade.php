<div class="assignment-create__form-group form-group">
    <label for="test_title_{{ $pageIndex }}" class="assignment-create__label">Название теста</label>
    <input type="text" id="test_title_{{ $pageIndex }}" name="pages[{{ $pageIndex }}][title]" class="assignment-create__input" required>
</div>
<div class="assignment-create__form-group form-group">
    <label for="test_description_{{ $pageIndex }}" class="assignment-create__label">Описание теста</label>
    <textarea id="test_description_{{ $pageIndex }}" name="pages[{{ $pageIndex }}][description]" class="assignment-create__textarea" rows="3"></textarea>
</div>
<div class="assignment-create__header-test">
    <div class="assignment-create__form-group form-group">
        <label for="test_time_{{ $pageIndex }}" class="assignment-create__label">Время на выполнение (в минутах)</label>
        <input type="number" id="test_time_{{ $pageIndex }}" name="pages[{{ $pageIndex }}][time_limit]" class="assignment-create__input" min="1" value="30">
    </div>
    <div class="assignment-create__form-group form-group">
        <label for="test_passing_{{ $pageIndex }}" class="assignment-create__label">Проходной балл</label>
        <input type="number" id="test_passing_{{ $pageIndex }}" name="pages[{{ $pageIndex }}][passing_score]" class="assignment-create__input" min="1" value="60">
    </div>
</div>