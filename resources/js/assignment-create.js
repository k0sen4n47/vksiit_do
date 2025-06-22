console.log('assignment-create.js loaded');

import { initTinyMCE } from './editors/tinymce-editor.js';
import { initCodeMirror } from './editors/codemirror-editor.js';

// Функция для очистки выбора предмета и группы
window.clearSelection = function() {
    console.log('clearSelection called');
    
    // Показываем поля выбора предмета и группы
    const subjectGroup = document.getElementById('subject-group');
    const groupGroup = document.getElementById('group-group');
    
    if (subjectGroup) {
        subjectGroup.style.display = 'block';
        const subjectSelect = subjectGroup.querySelector('select');
        if (subjectSelect) {
            subjectSelect.required = true;
        }
    }
    
    if (groupGroup) {
        groupGroup.style.display = 'block';
        const groupSelect = groupGroup.querySelector('select');
        if (groupSelect) {
            groupSelect.required = true;
        }
    }
    
    // Удаляем скрытые поля
    const hiddenSubject = document.querySelector('input[name="subject_id"][type="hidden"]');
    const hiddenGroup = document.querySelector('input[name="group_id"][type="hidden"]');
    
    if (hiddenSubject) hiddenSubject.remove();
    if (hiddenGroup) hiddenGroup.remove();
    
    // Скрываем информационную панель
    const infoPanel = document.querySelector('.assignment-create__info');
    if (infoPanel) {
        infoPanel.style.display = 'none';
    }
}

// Обработка тестов
let questionCounters = {};
let answerCounters = {};

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    // Инициализация счетчиков
    window.questionCounters = {};
    window.answerCounters = {};

    // Обработчик для кнопок добавления вопроса
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-question')) {
            const button = e.target.closest('.add-question');
            const testIndex = button.dataset.pageIndex;
            console.log('Adding question for test:', testIndex);
            createQuestion(testIndex);
        }
    });

    // Обработчик для кнопок удаления вопроса
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-question')) {
            const button = e.target.closest('.remove-question');
            const question = button.closest('.test-question');
            const testPage = question.closest('.assignment-create__page[data-page-type="test"]');
            if (!testPage) return;
            const testIndex = testPage.dataset.testIndex;
            question.remove();
            updateQuestionNumbers(testIndex);
        }
    });

    // Обработчик отправки формы
    const form = document.getElementById('assignmentForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }

    // --- Добавление текстовой страницы ---
    let pageIndex = 0;
    const addPageButton = document.querySelector('.assignment-create__add-page');
    const pagesContainer = document.querySelector('.assignment-create__pages');
    const textPageTemplate = document.getElementById('textPageTemplate');

    if (!addPageButton) {
        console.error('Кнопка .assignment-create__add-page не найдена!');
    } else {
        console.log('Кнопка .assignment-create__add-page найдена');
    }
    if (!pagesContainer) {
        console.error('Контейнер .assignment-create__pages не найден!');
    } else {
        console.log('Контейнер .assignment-create__pages найден');
    }
    if (!textPageTemplate) {
        console.error('Шаблон #textPageTemplate не найден!');
    } else {
        console.log('Шаблон #textPageTemplate найден');
    }

    if (addPageButton && pagesContainer && textPageTemplate) {
        addPageButton.addEventListener('click', function() {
            console.log('add page button clicked');
            // Показываем выбор типа страницы
            showPageTypeSelector(addPageButton, function(type) {
                pageIndex++;
                let templateId = '';
                if (type === 'text') templateId = 'textPageTemplate';
                if (type === 'code') templateId = 'codePageTemplate';
                if (type === 'test') templateId = 'testPageTemplate';
                const template = document.getElementById(templateId);
                if (!template) return;
                const clone = template.content.cloneNode(true);
                // Заменяем все INDEX или PAGE_INDEX на текущий индекс
                clone.querySelectorAll('[name], [for], [id], [data-page-index], [data-test-index]').forEach(el => {
                    if (el.name) el.name = el.name.replace(/INDEX|PAGE_INDEX/g, pageIndex);
                    if (el.htmlFor) el.htmlFor = el.htmlFor.replace(/INDEX|PAGE_INDEX/g, pageIndex);
                    if (el.id) el.id = el.id.replace(/INDEX|PAGE_INDEX/g, pageIndex);
                    if (el.dataset.pageIndex) el.dataset.pageIndex = pageIndex;
                    if (el.dataset.testIndex !== undefined) el.dataset.testIndex = pageIndex;
                });
                // Для отображения номера страницы человеку заменяем [PAGE_INDEX] на (pageIndex+1) в тексте
                clone.querySelectorAll('*').forEach(el => {
                    if (el.childNodes.length === 1 && el.childNodes[0].nodeType === 3) {
                        el.textContent = el.textContent.replace('[PAGE_INDEX]', (pageIndex + 1));
                    }
                });
                pagesContainer.appendChild(clone);
                // Инициализация редакторов
                if (type === 'text') {
                    setTimeout(() => initTinyMCE('.text-editor, .tinymce-editor'), 0);
                }
                if (type === 'code') {
                    setTimeout(() => {
                        const lastPage = pagesContainer.querySelector('.assignment-create__page[data-page-type="code"][data-page-index="' + pageIndex + '"]');
                        if (lastPage) initCodeMirror(lastPage);
                    }, 0);
                }
                if (type === 'test') {
                    // Здесь можно добавить инициализацию обработчиков для тестов, если нужно
                }
            });
        });
    }

    // Добавляем обработчик для удаления страницы
    document.addEventListener('click', function(e) {
        if (e.target.closest('.assignment-create__page-remove')) {
            const btn = e.target.closest('.assignment-create__page-remove');
            const page = btn.closest('.assignment-create__page');
            if (page) page.remove();
        }
    });

    // Обработчик для кнопок добавления ответа
    document.addEventListener('click', function(e) {
        if (e.target.closest('.add-answer')) {
            const button = e.target.closest('.add-answer');
            const question = button.closest('.test-question');
            if (!question) return;
            const testPage = question.closest('.assignment-create__page[data-page-type="test"]');
            if (!testPage) return;
            const testIndex = testPage.dataset.testIndex;
            const questionIndex = question.dataset.questionIndex;
            addAnswer(testIndex, questionIndex);
        }
    });

    // Обработчик для кнопок удаления ответа (делегированный)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-answer')) {
            const answer = e.target.closest('.test-answer');
            if (!answer) return;
            const question = answer.closest('.test-question');
            if (!question) return;
            const testPage = question.closest('.assignment-create__page[data-page-type="test"]');
            if (!testPage) return;
            const testIndex = testPage.dataset.testIndex;
            const questionIndex = question.dataset.questionIndex;
            answer.remove();
            updateAnswerNumbers(testIndex, questionIndex);
        }
    });

    // --- ИНИЦИАЛИЗАЦИЯ КОД-РЕДАКТОРА НА СТРАНИЦЕ ПРОСМОТРА ЗАДАНИЯ ---
    document.querySelectorAll('.assignment-create__edit-code').forEach(function(pageElement) {
        if (pageElement.querySelector('.html-editor') && pageElement.querySelector('.css-editor')) {
            // Заполняем редакторы начальными значениями из скрытых textarea
            const htmlTextarea = pageElement.querySelector('.html-textarea');
            const cssTextarea = pageElement.querySelector('.css-textarea');
            if (htmlTextarea && cssTextarea) {
                // Инициализируем CodeMirror
                setTimeout(() => {
                    initCodeMirror(pageElement);
                }, 0);
            }
        }
    });
});

// Функция обработки отправки формы
async function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    try {
        // Показываем индикатор загрузки
        submitButton.disabled = true;
        submitButton.textContent = 'Сохранение...';
        
        // Собираем данные формы
        const formData = new FormData(form);
        const assignmentData = collectAssignmentData(formData);
        
        // Отправляем данные на сервер
        const response = await fetch('/teacher/assignments/store', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(assignmentData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Показываем сообщение об успехе
            showAlert('success', result.message);
            
            // Перенаправляем на страницу задания
            setTimeout(() => {
                window.location.href = result.redirect_url;
            }, 1500);
        } else {
            showAlert('error', result.message || 'Произошла ошибка при сохранении задания');
        }
        
    } catch (error) {
        console.error('Error submitting form:', error);
        showAlert('error', 'Произошла ошибка при сохранении задания');
    } finally {
        // Восстанавливаем кнопку
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
}

// Функция сбора данных задания
function collectAssignmentData(formData) {
    let deadline = formData.get('deadline');
    // Преобразуем T в пробел и добавляем :00 секунд, если их нет
    if (deadline && deadline.includes('T')) {
        deadline = deadline.replace('T', ' ');
        if (!/:\d{2}$/.test(deadline)) {
            deadline += ':00';
        }
    }
    const data = {
        subject_id: formData.get('subject_id'),
        group_id: formData.get('group_id'),
        title: formData.get('title'),
        description: formData.get('description'),
        deadline: deadline,
        max_score: parseInt(formData.get('max_score')) || 100,
        pages: []
    };
    // Собираем данные страниц
    const pageContainers = document.querySelectorAll('.assignment-create__page');
    let hasError = false;
    let errorMsg = '';
    pageContainers.forEach((container, index) => {
        const pageType = container.dataset.pageType;
        let pageTitle = '';
        let pageContent = '';
        if (pageType === 'text') {
            pageTitle = container.querySelector('input[name*="[title]"]').value;
            pageContent = container.querySelector('textarea[name*="[content]"]').value;
            if (!pageTitle) { hasError = true; errorMsg = 'У текстовой страницы нет заголовка'; }
        } else if (pageType === 'code') {
            pageTitle = container.querySelector('input[name*="[title]"]').value;
            // Ищем поля HTML и CSS с правильными селекторами
            const htmlField = container.querySelector('textarea[name*="[html]"]');
            const cssField = container.querySelector('textarea[name*="[css]"]');
            
            if (!htmlField || !cssField) {
                console.error('HTML или CSS поля не найдены в странице с кодом');
                hasError = true;
                errorMsg = 'Ошибка: поля HTML или CSS не найдены';
            } else {
                pageContent = JSON.stringify({
                    html: htmlField.value,
                    css: cssField.value
                });
            }
            
            if (!pageTitle) { hasError = true; errorMsg = 'У страницы с кодом нет заголовка'; }
        } else if (pageType === 'test') {
            pageTitle = container.querySelector('input[name*="[title]"]').value;
            pageContent = container.querySelector('textarea[name*="[description]"]').value;
            if (!pageTitle) { hasError = true; errorMsg = 'У тестовой страницы нет заголовка'; }
        }
        const pageData = {
            type: pageType,
            title: pageTitle,
            order: index + 1,
            content: pageContent
        };
        if (pageType === 'test') {
            const testData = collectTestData(container);
            if (testData.error) {
                hasError = true;
                errorMsg = testData.error;
            } else {
                pageData.test = testData;
            }
        }
        if (!hasError) {
            data.pages.push(pageData);
        }
    });
    if (hasError) {
        showAlert('error', errorMsg);
        throw new Error(errorMsg);
    }
    return data;
}

// Функция сбора данных теста
function collectTestData(testContainer) {
    const testData = {
        title: testContainer.querySelector('input[name*="[title]"]').value,
        description: testContainer.querySelector('textarea[name*="[description]"]')?.value || '',
        time_limit: testContainer.querySelector('input[name*="[time_limit]"]')?.value || null,
        passing_score: parseInt(testContainer.querySelector('input[name*="[passing_score]"]').value),
        max_attempts: testContainer.querySelector('input[name*="[max_attempts]"]')?.value || null,
        shuffle_questions: testContainer.querySelector('input[name*="[shuffle_questions]"]')?.checked || false,
        show_results: testContainer.querySelector('input[name*="[show_results]"]')?.checked || true,
        questions: []
    };
    if (!testData.title) return { error: 'У теста нет названия' };
    if (!testData.passing_score || isNaN(testData.passing_score)) return { error: 'У теста не указан проходной балл' };
    const questions = testContainer.querySelectorAll('.test-question');
    if (questions.length === 0) return { error: 'В тесте должен быть хотя бы один вопрос' };
    questions.forEach((question, qIdx) => {
        const questionType = question.querySelector('.question-type').value;
        const questionText = question.querySelector('.question-text').value;
        const points = parseInt(question.querySelector('.question-score').value);
        if (!questionText) return;
        if (!points || isNaN(points)) return;
        const questionData = {
            question_text: questionText,
            type: questionType,
            points: points
        };
        if (questionType === 'single' || questionType === 'multiple') {
            const answers = question.querySelectorAll('.test-answer');
            if (answers.length === 0) {
                testData.error = `Вопрос №${qIdx+1} должен содержать хотя бы один ответ`;
                return;
            }
            let hasCorrect = false;
            questionData.answers = [];
            answers.forEach(answer => {
                const answerText = answer.querySelector('.answer-text').value;
                const isCorrect = answer.querySelector('.answer-correct')?.checked || false;
                if (isCorrect) hasCorrect = true;
                if (answerText) {
                    questionData.answers.push({
                        answer_text: answerText,
                        is_correct: isCorrect
                    });
                }
            });
            if (!hasCorrect) {
                testData.error = `В вопросе №${qIdx+1} не отмечен правильный ответ`;
                return;
            }
        }
        // Для text-вопроса поле answers не добавляем вообще!
        testData.questions.push(questionData);
    });
    if (testData.questions.length === 0) return { error: 'В тесте нет валидных вопросов' };
    if (testData.error) return { error: testData.error };
    return testData;
}

// Функция показа уведомлений
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
    
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.innerHTML = alertHtml;
    alertContainer.style.display = 'block';
    
    // Автоматически скрываем через 5 секунд
    setTimeout(() => {
        alertContainer.style.display = 'none';
    }, 5000);
}

// Функция создания контейнера для уведомлений
function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alertContainer';
    container.style.position = 'fixed';
    container.style.top = '20px';
    container.style.right = '20px';
    container.style.zIndex = '9999';
    container.style.display = 'none';
    
    document.body.appendChild(container);
    return container;
}

// Функция для создания нового вопроса
function createQuestion(testIndex) {
    console.log('Creating question for test:', testIndex);
    const testContainer = document.querySelector(`[data-test-index="${testIndex}"]`);
    if (!testContainer) {
        console.error('Test container not found');
        return;
    }
    const questionsContainer = testContainer.querySelector('.questions-container');
    if (!questionsContainer) {
        console.error('Questions container not found');
        return;
    }
    // Получаем текущий индекс вопроса
    const questionIndex = questionsContainer.children.length;
    // Клонируем шаблон вопроса
    const template = document.getElementById('question-template');
    if (!template) {
        console.error('Question template not found');
        return;
    }
    const newQuestion = template.content.cloneNode(true);
    const questionElement = newQuestion.querySelector('.test-question');
    // Устанавливаем индекс вопроса
    questionElement.dataset.questionIndex = questionIndex;
    // Обновляем номера вопросов
    const questionNumber = questionElement.querySelector('.question-number');
    if (questionNumber) {
        questionNumber.textContent = questionIndex + 1;
    }
    // Устанавливаем классы для полей, если вдруг их нет
    const qText = questionElement.querySelector('input[type="text"]');
    if (qText && !qText.classList.contains('question-text')) qText.classList.add('question-text');
    const qType = questionElement.querySelector('select');
    if (qType && !qType.classList.contains('question-type')) qType.classList.add('question-type');
    const qScore = questionElement.querySelector('input[type="number"]');
    if (qScore && !qScore.classList.contains('question-score')) qScore.classList.add('question-score');
    // Добавляем вопрос в контейнер
    questionsContainer.appendChild(questionElement);
}

// Функция для создания нового ответа
function createAnswer(questionElement) {
    const answersContainer = questionElement.querySelector('.answers-container');
    if (!answersContainer) return;
    const answerIndex = answersContainer.children.length;
    const template = document.getElementById('answer-template');
    if (!template) return;
    const newAnswer = template.content.cloneNode(true);
    const answerElement = newAnswer.querySelector('.test-answer');
    // Устанавливаем классы для полей, если вдруг их нет
    const aText = answerElement.querySelector('input[type="text"]');
    if (aText && !aText.classList.contains('answer-text')) aText.classList.add('answer-text');
    const aCorrect = answerElement.querySelector('input[type="radio"]');
    if (aCorrect && !aCorrect.classList.contains('answer-correct')) aCorrect.classList.add('answer-correct');
    answersContainer.appendChild(answerElement);
}

// Функция для добавления ответа
function addAnswer(testIndex, questionIndex) {
    console.log('Adding answer for question:', questionIndex, 'in test:', testIndex);
    
    if (!window.answerCounters[testIndex]) {
        window.answerCounters[testIndex] = {};
    }
    if (!window.answerCounters[testIndex][questionIndex]) {
        window.answerCounters[testIndex][questionIndex] = 0;
    }
    const answerIndex = window.answerCounters[testIndex][questionIndex]++;
    
    const answerTemplate = document.getElementById('answer-template');
    if (!answerTemplate) {
        console.error('Answer template not found');
        return;
    }

    const answerHtml = answerTemplate.innerHTML
        .replace(/\[TEST_INDEX\]/g, testIndex)
        .replace(/\[QUESTION_INDEX\]/g, questionIndex)
        .replace(/\[ANSWER_INDEX\]/g, answerIndex);
    
    const question = document.querySelector(`[data-test-index="${testIndex}"] .test-question:nth-child(${questionIndex + 1})`);
    if (!question) {
        console.error('Question not found:', questionIndex);
        return;
    }

    let answersContainer = question.querySelector('.answers-container');
    if (!answersContainer) {
        answersContainer = question.querySelector('.test-answers__container');
    }
    if (!answersContainer) {
        console.error('Answers container not found for question:', questionIndex);
        return;
    }

    answersContainer.insertAdjacentHTML('beforeend', answerHtml);
    
    // Навешиваем обработчик на radio/checkbox для правильного ответа
    const correctInput = answersContainer.lastElementChild.querySelector('.form-check-input');
    if (correctInput) {
        correctInput.addEventListener('change', handleCorrectAnswerChange);
    }
}

// Функция обновления номеров вопросов
function updateQuestionNumbers(testIndex) {
    const testContainer = document.querySelector(`[data-test-index="${testIndex}"]`);
    if (!testContainer) return;

    const questions = testContainer.querySelectorAll('.test-question');
    questions.forEach((question, index) => {
        const numberElement = question.querySelector('.question-number');
        if (numberElement) {
            numberElement.textContent = index + 1;
        }
        question.dataset.questionIndex = index;
        
        // Обновляем имена полей
        question.querySelectorAll('[name*="questions["]').forEach(element => {
            const newName = element.name.replace(/questions\[\d+\]/, `questions[${index}]`);
            element.name = newName;
        });
    });
}

// Функция для обновления нумерации ответов
function updateAnswerNumbers(testIndex, questionIndex) {
    const question = document.querySelector(`[data-test-index="${testIndex}"] .test-question:nth-child(${questionIndex + 1})`);
    if (!question) return;

    const answers = question.querySelectorAll('.test-answer');
    answers.forEach((answer, index) => {
        const header = answer.querySelector('h5');
        if (header) {
            header.textContent = `Ответ ${index + 1}`;
        }
        // Обновляем индексы в name атрибутах
        answer.querySelectorAll('[name*="answers]"]').forEach(input => {
            input.name = input.name.replace(/answers\[\d+\]/, `answers[${index}]`);
        });
    });
}

// Обработчик изменения типа вопроса
function handleQuestionTypeChange(event) {
    const questionContainer = event.target.closest('.test-question');
    const answersContainer = questionContainer.querySelector('.answers-container');
    const answersList = answersContainer.querySelector('.answers-list');
    const textAnswer = answersContainer.querySelector('.text-answer');
    const questionType = event.target.value;
    
    if (questionType === 'text') {
        // Показываем поле для текстового ответа
        if (answersList) answersList.style.display = 'none';
        if (textAnswer) textAnswer.style.display = 'block';
    } else {
        // Показываем список ответов
        if (answersList) answersList.style.display = 'block';
        if (textAnswer) textAnswer.style.display = 'none';
        
        // Обновляем тип input для правильных ответов
        const correctAnswerInputs = answersContainer.querySelectorAll('.form-check-input');
        correctAnswerInputs.forEach(input => {
            input.type = questionType === 'multiple' ? 'checkbox' : 'radio';
            input.name = `correct_answer_${questionContainer.dataset.questionIndex}`;
        });
    }
}

// Обработчик выбора правильного ответа
function handleCorrectAnswerChange(event) {
    const answerContainer = event.target.closest('.test-answer');
    const questionContainer = answerContainer.closest('.test-question');
    const questionType = questionContainer.querySelector('.question-type').value;
    
    if (questionType === 'single') {
        // Для одиночного выбора снимаем выделение с других ответов
        const otherAnswers = questionContainer.querySelectorAll('.test-answer');
        otherAnswers.forEach(answer => {
            if (answer !== answerContainer) {
                answer.classList.remove('correct');
                const checkbox = answer.querySelector('.form-check-input');
                if (checkbox) checkbox.checked = false;
            }
        });
    }
    
    // Обновляем класс correct для контейнера ответа
    if (event.target.checked) {
        answerContainer.classList.add('correct');
    } else {
        answerContainer.classList.remove('correct');
    }
}

// Добавляем обработчики событий при создании вопроса
function addQuestionEventListeners(questionContainer) {
    const questionTypeSelect = questionContainer.querySelector('.question-type');
    if (questionTypeSelect) {
        questionTypeSelect.addEventListener('change', handleQuestionTypeChange);
    }
    // Навешиваем обработчик на все radio/checkbox в уже существующих ответах
    const correctAnswerInputs = questionContainer.querySelectorAll('.form-check-input');
    correctAnswerInputs.forEach(input => {
        input.addEventListener('change', handleCorrectAnswerChange);
    });
}

// Показывает выбор типа страницы (div внутри формы, а не модалка)
function showPageTypeSelector(anchor, callback) {
    // Если уже есть селектор — не добавлять второй
    if (document.getElementById('pageTypeSelector')) return;
    const selector = document.createElement('div');
    selector.id = 'pageTypeSelector';
    selector.style.marginTop = '10px';
    selector.innerHTML = `
        <button type="button" class="btn-cabinet" data-type="code">Код</button>
        <button type="button" class="btn-cabinet" data-type="text">Текст</button>
        <button type="button" class="btn-cabinet" data-type="test">Тест</button>
        <button type="button" class="btn-cabinet" id="cancelPageType">Отмена</button>
    `;
    const pagesContainer = document.querySelector('.assignment-create__pages');
    if (pagesContainer) {
        pagesContainer.appendChild(selector);
    } else {
        anchor.parentNode.insertBefore(selector, anchor.nextSibling);
    }
    selector.querySelectorAll('button[data-type]').forEach(btn => {
        btn.onclick = function() {
            callback(btn.getAttribute('data-type'));
            selector.remove();
        };
    });
    selector.querySelector('#cancelPageType').onclick = function() {
        selector.remove();
    };
} 