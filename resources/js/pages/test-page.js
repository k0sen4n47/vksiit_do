export async function createTestPage(pageIndex) {
    console.log('Creating test page with index:', pageIndex);
    
    // Создаем HTML шаблон статически
    const html = `
        <div class="assignment-create__page test" data-page-index="${pageIndex}" data-page-type="test">
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
                <div class="assignment-create__form-group form-group">
                    <button type="button" class="add-question btn">
                        <i class="fas fa-plus"></i> Добавить вопрос
                    </button>
                </div>
                <div class="test-questions__container" style="display: none;">
                    <!-- Здесь будут добавляться вопросы -->
                </div>
            </div>
        </div>
    `;
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;
    const pageElement = tempDiv.firstElementChild;

    // Добавляем обработчики событий
    const addQuestionButton = pageElement.querySelector('.add-question');
    const questionsContainer = pageElement.querySelector('.test-questions__container');

    // Проверяем, что элементы найдены
    if (!addQuestionButton) {
        console.error('Add question button not found');
    }
    if (!questionsContainer) {
        console.error('Questions container not found');
    }

    // Функция для обновления видимости контейнера вопросов
    function updateQuestionsContainerVisibility() {
        if (!questionsContainer) return;
        
        const questions = questionsContainer.querySelectorAll('.test-question');
        if (questions.length === 0) {
            questionsContainer.style.display = 'none';
        } else {
            questionsContainer.style.display = 'block';
        }
    }

    // Инициализируем видимость контейнера вопросов
    updateQuestionsContainerVisibility();

    if (addQuestionButton && questionsContainer) {
        addQuestionButton.addEventListener('click', async () => {
            const questionIndex = questionsContainer.children.length;
            const questionElement = await createQuestion(pageIndex, questionIndex);
            questionsContainer.appendChild(questionElement);
            updateQuestionsContainerVisibility();
        });
    }

    console.log('Created test page element:', pageElement);
    return pageElement;
}

async function createQuestion(pageIndex, questionIndex) {
    console.log('Creating question with pageIndex:', pageIndex, 'questionIndex:', questionIndex);
    
    // Создаем HTML шаблон статически
    const html = `
        <div class="test-question" data-question-index="${questionIndex}">
            <div class="test-question__header">
                <h4>Вопрос ${questionIndex + 1}</h4>
                <button type="button" class="remove-question btn btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="test-question__body">
                <div class="assignment-create__form-group form-group">
                    <label class="assignment-create__label">Текст вопроса</label>
                    <textarea name="pages[${pageIndex}][questions][${questionIndex}][text]" class="assignment-create__textarea" rows="3" required></textarea>
                </div>
                <div class="assignment-create__form-group form-group">
                    <label class="assignment-create__label">Тип вопроса</label>
                    <select class="question-type" name="pages[${pageIndex}][questions][${questionIndex}][type]">
                        <option value="single">Один правильный ответ</option>
                        <option value="multiple">Несколько правильных ответов</option>
                        <option value="text">Текстовый ответ</option>
                    </select>
                </div>
                <div class="assignment-create__form-group form-group">
                    <label class="assignment-create__label">Изображение к вопросу (опционально)</label>
                    <input type="file" class="question-image" accept="image/*">
                    <div class="question-image-preview" style="display: none;">
                        <img src="" alt="Preview" style="max-width: 200px; max-height: 200px;">
                    </div>
                </div>
                <div class="assignment-create__form-group form-group">
                    <button type="button" class="add-answer btn">
                        <i class="fas fa-plus"></i> Добавить ответ
                    </button>
                </div>
                <div class="test-answers__container" style="display: none;">
                    <!-- Здесь будут добавляться ответы -->
                </div>
                <div class="text-answer-container" style="display: none;">
                    <div class="assignment-create__form-group form-group">
                        <label class="assignment-create__label">Правильный ответ</label>
                        <textarea name="pages[${pageIndex}][questions][${questionIndex}][correct_answer]" class="assignment-create__textarea" rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;
    const questionElement = tempDiv.firstElementChild;

    // Добавляем обработчики событий
    const addAnswerButton = questionElement.querySelector('.add-answer');
    const answersContainer = questionElement.querySelector('.test-answers__container');
    const questionType = questionElement.querySelector('.question-type');
    const removeQuestionButton = questionElement.querySelector('.remove-question');
    const imageInput = questionElement.querySelector('.question-image');
    const imagePreview = questionElement.querySelector('.question-image-preview');
    const textAnswerContainer = questionElement.querySelector('.text-answer-container');

    // Функция для обновления видимости контейнера ответов
    function updateAnswersContainerVisibility() {
        if (!answersContainer) return;
        
        const answers = answersContainer.querySelectorAll('.test-answer');
        if (answers.length === 0) {
            answersContainer.style.display = 'none';
        } else {
            answersContainer.style.display = 'block';
        }
    }

    // Инициализируем видимость контейнера ответов
    updateAnswersContainerVisibility();

    // Обработчик изменения типа вопроса
    if (questionType) {
        questionType.addEventListener('change', (e) => {
            const type = e.target.value;
            const answers = answersContainer.querySelectorAll('.test-answer');
            
            if (type === 'text') {
                // Скрываем контейнер с вариантами ответов и показываем поле для текстового ответа
                if (answersContainer) answersContainer.style.display = 'none';
                if (addAnswerButton) addAnswerButton.style.display = 'none';
                if (textAnswerContainer) textAnswerContainer.style.display = 'block';
            } else {
                // Показываем контейнер с вариантами ответов и скрываем поле для текстового ответа
                if (textAnswerContainer) textAnswerContainer.style.display = 'none';
                if (addAnswerButton) addAnswerButton.style.display = 'block';
                updateAnswersContainerVisibility();
                
                // Обновляем тип input для правильных ответов
                answers.forEach(answer => {
                    const checkbox = answer.querySelector('.answer-correct');
                    if (checkbox) {
                        if (type === 'multiple') {
                            checkbox.type = 'checkbox';
                            checkbox.name = `pages[${pageIndex}][questions][${questionIndex}][correct_answers][]`;
                        } else {
                            checkbox.type = 'radio';
                            checkbox.name = `pages[${pageIndex}][questions][${questionIndex}][correct_answer]`;
                        }
                    }
                });
            }
        });
    }

    // Обработчик загрузки изображения
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = imagePreview.querySelector('img');
                    if (img) {
                        img.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (addAnswerButton && answersContainer) {
        addAnswerButton.addEventListener('click', async () => {
            const answerIndex = answersContainer.children.length;
            const answerElement = await createAnswer(pageIndex, questionIndex, answerIndex);
            
            // Устанавливаем правильный тип input в зависимости от типа вопроса
            const type = questionType.value;
            const checkbox = answerElement.querySelector('.answer-correct');
            if (checkbox) {
                if (type === 'multiple') {
                    checkbox.type = 'checkbox';
                    checkbox.name = `pages[${pageIndex}][questions][${questionIndex}][correct_answers][]`;
                } else {
                    checkbox.type = 'radio';
                    checkbox.name = `pages[${pageIndex}][questions][${questionIndex}][correct_answer]`;
                }
            }
            
            answersContainer.appendChild(answerElement);
            updateAnswersContainerVisibility();
        });
    }

    if (removeQuestionButton) {
        removeQuestionButton.addEventListener('click', () => {
            questionElement.remove();
            // Обновляем видимость контейнера вопросов после удаления
            const questionsContainer = questionElement.closest('.test-questions__container');
            if (questionsContainer) {
                const questions = questionsContainer.querySelectorAll('.test-question');
                if (questions.length === 0) {
                    questionsContainer.style.display = 'none';
                }
            }
        });
    }

    console.log('Created question element:', questionElement);
    return questionElement;
}

async function createAnswer(pageIndex, questionIndex, answerIndex) {
    console.log('Creating answer with pageIndex:', pageIndex, 'questionIndex:', questionIndex, 'answerIndex:', answerIndex);
    
    // Создаем HTML шаблон статически
    const html = `
        <div class="test-answer" data-answer-index="${answerIndex}">
            <div class="test-answer__header">
                <h5>Ответ ${answerIndex + 1}</h5>
                <button type="button" class="remove-answer btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="test-answer__body">
                <div class="assignment-create__form-group form-group">
                    <label class="assignment-create__label">Текст ответа</label>
                    <input type="text" name="pages[${pageIndex}][questions][${questionIndex}][answers][${answerIndex}][text]" class="assignment-create__input" required>
                </div>
                <div class="assignment-create__form-group form-group">
                    <label class="assignment-create__label">
                        <input type="radio" class="answer-correct" name="pages[${pageIndex}][questions][${questionIndex}][correct_answer]" value="${answerIndex}">
                        Правильный ответ
                    </label>
                </div>
            </div>
        </div>
    `;
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = html;
    const answerElement = tempDiv.firstElementChild;

    // Добавляем обработчик удаления ответа
    const removeAnswerButton = answerElement.querySelector('.remove-answer');
    if (removeAnswerButton) {
        removeAnswerButton.addEventListener('click', () => {
            answerElement.remove();
            // Обновляем видимость контейнера ответов после удаления
            const answersContainer = answerElement.closest('.test-answers__container');
            if (answersContainer) {
                const answers = answersContainer.querySelectorAll('.test-answer');
                if (answers.length === 0) {
                    answersContainer.style.display = 'none';
                }
            }
        });
    }

    console.log('Created answer element:', answerElement);
    return answerElement;
} 