import { createTextPage } from './pages/text-page.js';
import { createCodePage } from './pages/code-page.js';
import { createTestPage } from './pages/test-page.js';
import { initTinyMCE } from './editors/tinymce-editor.js';
import { initCodeMirror } from './editors/codemirror-editor.js';

document.addEventListener('DOMContentLoaded', function() {
    console.log('Assignment Modal JS loaded');
    
    // Инициализируем TinyMCE для существующих редакторов
    initTinyMCE();
    
    let pageCount = 0;
    const pagesContainer = document.querySelector('.assignment-create__pages');
    
    if (!pagesContainer) {
        console.error('Container for pages not found');
        return;
    }

    // Добавляем обработчики для существующих кнопок удаления
    const existingRemoveButtons = document.querySelectorAll('.assignment-create__page-remove');
    existingRemoveButtons.forEach(button => {
        button.addEventListener('click', () => {
            const pageContainer = button.closest('.assignment-create__page');
            if (pageContainer) {
                pageContainer.remove();
                updatePageIndexes();
            }
        });
    });

    function createPage(pageType) {
        console.log('Creating page of type:', pageType);
        
        const pagesContainer = document.querySelector('.assignment-create__pages');
        if (!pagesContainer) {
            console.error('Container for pages not found');
            return;
        }

        const pageIndex = pagesContainer.children.length;
        let pageElement;

        switch (pageType) {
            case 'text':
                pageElement = createTextPage(pageIndex);
                addPageToContainer(pageElement, pageIndex, pageType);
                break;
            case 'code':
                createCodePage(pageIndex)
                    .then(element => {
                        addPageToContainer(element, pageIndex, pageType);
                    })
                    .catch(error => {
                        console.error('Error creating code page:', error);
                    });
                break;
            case 'test':
                createTestPage(pageIndex)
                    .then(element => {
                        addPageToContainer(element, pageIndex, pageType);
                    })
                    .catch(error => {
                        console.error('Error creating test page:', error);
                    });
                break;
            default:
                console.error('Unknown page type:', pageType);
                return;
        }
    }

    function addPageToContainer(pageElement, pageIndex, pageType) {
        const pagesContainer = document.querySelector('.assignment-create__pages');
        if (!pagesContainer) {
            console.error('Container for pages not found');
            return;
        }

        // Добавляем скрытые поля для формы
        const hiddenInputs = document.createElement('div');
        hiddenInputs.style.display = 'none';
        
        // Добавляем поле для типа страницы
        const typeInput = document.createElement('input');
        typeInput.type = 'hidden';
        typeInput.name = `pages[${pageIndex}][type]`;
        typeInput.value = pageType;
        hiddenInputs.appendChild(typeInput);

        // Добавляем контейнер для контента страницы
        const contentContainer = document.createElement('div');
        contentContainer.className = 'page-content';
        contentContainer.setAttribute('data-page-index', pageIndex);
        contentContainer.setAttribute('data-page-type', pageType);

        // Добавляем элементы в контейнер страницы
        const pageContainer = document.createElement('div');
        pageContainer.className = 'assignment-create__page';
        pageContainer.setAttribute('data-page-index', pageIndex);
        pageContainer.appendChild(hiddenInputs);
        pageContainer.appendChild(contentContainer);
        pageContainer.appendChild(pageElement);

        pagesContainer.appendChild(pageContainer);
        pageCount++;

        // Инициализируем редакторы в зависимости от типа страницы
        if (pageType === 'code') {
            // Инициализируем CodeMirror
            setTimeout(() => {
                initCodeMirror(pageContainer);
            }, 100);
        } else if (pageType === 'text') {
            // Инициализируем TinyMCE
            setTimeout(() => {
                const textarea = pageElement.querySelector('.tinymce-editor');
                if (textarea && !textarea.id) {
                    textarea.id = 'tinymce-' + Math.random().toString(36).substr(2, 9);
                }
                if (textarea && textarea.id) {
                    initTinyMCE('#' + textarea.id);
                }
            }, 100);
        }

        // Добавляем обработчик для кнопки удаления страницы
        const removeButton = pageContainer.querySelector('.assignment-create__page-remove');
        if (removeButton) {
            removeButton.addEventListener('click', () => {
                pageContainer.remove();
                // Обновляем индексы оставшихся страниц
                updatePageIndexes();
            });
        }
    }

    // Функция для обновления индексов страниц после удаления
    function updatePageIndexes() {
        const pages = document.querySelectorAll('.assignment-create__page');
        pages.forEach((page, index) => {
            page.setAttribute('data-page-index', index);
            
            // Обновляем скрытые поля
            const typeInput = page.querySelector('input[name*="[type]"]');
            if (typeInput) {
                typeInput.name = `pages[${index}][type]`;
            }

            // Обновляем поля заголовка
            const titleInput = page.querySelector('input[name*="[title]"]');
            if (titleInput) {
                titleInput.name = `pages[${index}][title]`;
            }

            // Обновляем поля описания
            const descInput = page.querySelector('textarea[name*="[description]"]');
            if (descInput) {
                descInput.name = `pages[${index}][description]`;
            }

            // Обновляем кнопку удаления
            const removeButton = page.querySelector('.assignment-create__page-remove');
            if (removeButton) {
                removeButton.setAttribute('data-page-index', index);
            }

            // Обновляем контейнер контента
            const contentContainer = page.querySelector('.page-content');
            if (contentContainer) {
                contentContainer.setAttribute('data-page-index', index);
            }
        });
    }

    // Обработчик кнопки добавления страницы
    const addPageButton = document.querySelector('.assignment-create__add-page');
    if (addPageButton) {
        addPageButton.addEventListener('click', () => {
            // Удаляем существующие кнопки выбора типа, если они есть
            const existingButtons = document.querySelector('.page-type-buttons');
            if (existingButtons) {
                existingButtons.remove();
            }

            // Создаем кнопки выбора типа страницы
            const buttonsContainer = document.createElement('div');
            buttonsContainer.className = 'page-type-buttons';

            const buttonTypes = [
                { type: 'text', label: 'Текст' },
                { type: 'code', label: 'Код' },
                { type: 'test', label: 'Тест' }
            ];

            buttonTypes.forEach(btn => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'btn';
                button.textContent = btn.label;

                button.addEventListener('click', () => {
                    createPage(btn.type);
                    buttonsContainer.remove();
                });

                buttonsContainer.appendChild(button);
            });

            // Добавляем кнопки в контейнер страниц
            pagesContainer.appendChild(buttonsContainer);
        });
    }

    // Обработчик отправки формы
    const form = document.querySelector('.assignment-create__form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Form submission started');
            
            // Собираем основные данные формы
            const formData = new FormData(form);
            const assignmentData = {
                title: formData.get('title'),
                description: formData.get('description'),
                subject_id: formData.get('subject_id'),
                group_id: formData.get('group_id'),
                max_score: parseInt(formData.get('max_score')),
                deadline: formData.get('deadline'),
                pages: []
            };

            // Собираем данные страниц
            const pages = document.querySelectorAll('.assignment-create__page');
            pages.forEach((page, index) => {
                const pageIndex = page.getAttribute('data-page-index') || index;
                const pageType = page.querySelector('input[name*="[type]"]')?.value;
                
                if (!pageType) {
                    console.error('Page type not found for page', index);
                    return;
                }

                const pageData = {
                    type: pageType,
                    order: parseInt(pageIndex) + 1,
                    title: '',
                    content: ''
                };

                // Получаем заголовок страницы
                const titleInput = page.querySelector('input[name*="[title]"]');
                if (titleInput) {
                    pageData.title = titleInput.value;
                }

                // Получаем контент в зависимости от типа страницы
                switch (pageType) {
                    case 'text':
                        const textarea = page.querySelector('.tinymce-editor');
                        if (textarea && textarea.id) {
                            // Получаем контент из TinyMCE
                            if (window.tinymce && window.tinymce.get(textarea.id)) {
                                pageData.content = window.tinymce.get(textarea.id).getContent();
                            } else {
                                pageData.content = textarea.value;
                            }
                        }
                        break;
                        
                    case 'code':
                        // Пытаемся получить контент из CodeMirror
                        const codeEditor = page.querySelector('.CodeMirror');
                        if (codeEditor && codeEditor.CodeMirror) {
                            pageData.content = codeEditor.CodeMirror.getValue();
                        } else {
                            // Fallback: получаем контент из скрытых textarea
                            const htmlTextarea = page.querySelector('textarea[name*="[html]"]');
                            const cssTextarea = page.querySelector('textarea[name*="[css]"]');
                            
                            if (htmlTextarea && cssTextarea) {
                                pageData.content = {
                                    html: htmlTextarea.value,
                                    css: cssTextarea.value
                                };
                            } else {
                                pageData.content = '';
                            }
                        }
                        break;
                        
                    case 'test':
                        // Для тестов собираем данные теста
                        const testData = collectTestData(page);
                        if (testData) {
                            pageData.test = testData;
                        }
                        break;
                }

                assignmentData.pages.push(pageData);
            });

            console.log('Collected assignment data:', assignmentData);

            // Проверяем обязательные поля
            const requiredFields = ['title', 'description', 'subject_id', 'group_id', 'max_score', 'deadline'];
            const missingFields = requiredFields.filter(field => !assignmentData[field]);
            
            if (missingFields.length > 0) {
                console.error('Missing required fields:', missingFields);
                alert('Пожалуйста, заполните все обязательные поля: ' + missingFields.join(', '));
                return;
            }

            if (assignmentData.pages.length === 0) {
                console.error('No pages found');
                alert('Пожалуйста, добавьте хотя бы одну страницу');
                return;
            }

            // Проверяем каждую страницу
            assignmentData.pages.forEach((page, index) => {
                console.log(`Page ${index}:`, page);
                if (!page.title) {
                    console.error(`Page ${index} missing title`);
                }
                if (!page.type) {
                    console.error(`Page ${index} missing type`);
                }
                if (!page.order) {
                    console.error(`Page ${index} missing order`);
                }
            });

            // Отправляем данные на сервер
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(assignmentData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = data.redirect_url;
                } else {
                    alert('Ошибка: ' + data.message);
                    if (data.errors) {
                        console.error('Validation errors:', data.errors);
                    }
                }
            })
            .catch(error => {
                console.error('Error submitting form:', error);
                alert('Произошла ошибка при отправке формы');
            });
        });
    }

    // Функция для сбора данных теста
    function collectTestData(pageElement) {
        console.log('Collecting test data from page element:', pageElement);
        
        const testData = {
            title: '',
            description: '',
            time_limit: null,
            passing_score: 60,
            max_attempts: 1,
            shuffle_questions: false,
            show_results: true,
            questions: []
        };

        // Получаем заголовок теста (используем заголовок страницы)
        const titleInput = pageElement.querySelector('input[name*="[title]"]');
        if (titleInput) {
            testData.title = titleInput.value;
        }

        // Получаем описание теста (используем описание страницы)
        const descInput = pageElement.querySelector('textarea[name*="[description]"]');
        if (descInput) {
            testData.description = descInput.value;
        }

        // Получаем вопросы
        const questions = pageElement.querySelectorAll('.test-question');
        console.log('Found questions:', questions.length);
        
        questions.forEach((questionElement, questionIndex) => {
            const questionData = {
                question_text: '',
                type: 'single',
                points: 1,
                answers: []
            };

            // Получаем текст вопроса
            const questionTextInput = questionElement.querySelector('textarea[name*="[text]"]');
            if (questionTextInput) {
                questionData.question_text = questionTextInput.value;
            }

            // Получаем тип вопроса
            const questionTypeSelect = questionElement.querySelector('.question-type');
            if (questionTypeSelect) {
                questionData.type = questionTypeSelect.value;
            }

            // Получаем ответы для закрытых вопросов
            if (['single', 'multiple'].includes(questionData.type)) {
                const answers = questionElement.querySelectorAll('.test-answer');
                console.log('Found answers for question', questionIndex, ':', answers.length);
                
                answers.forEach((answerElement, answerIndex) => {
                    const answerData = {
                        answer_text: '',
                        is_correct: false
                    };

                    const answerTextInput = answerElement.querySelector('input[name*="[text]"]');
                    if (answerTextInput) {
                        answerData.answer_text = answerTextInput.value;
                    }

                    const answerCorrectInput = answerElement.querySelector('.answer-correct');
                    if (answerCorrectInput) {
                        answerData.is_correct = answerCorrectInput.checked;
                    }

                    questionData.answers.push(answerData);
                });
            }

            testData.questions.push(questionData);
        });

        console.log('Collected test data:', testData);
        return testData;
    }

    // Функция для очистки предзаполненных полей
    window.clearSelection = function() {
        const subjectGroup = document.getElementById('subject-group');
        const groupGroup = document.getElementById('group-group');
        const subjectHidden = document.querySelector('input[name="subject_id"][type="hidden"]');
        const groupHidden = document.querySelector('input[name="group_id"][type="hidden"]');
        
        // Показываем скрытые поля
        if (subjectGroup) {
            subjectGroup.style.display = 'block';
        }
        
        if (groupGroup) {
            groupGroup.style.display = 'block';
        }
        
        // Удаляем скрытые поля
        if (subjectHidden) {
            subjectHidden.remove();
        }
        
        if (groupHidden) {
            groupHidden.remove();
        }
        
        // Очищаем значения в селектах
        const subjectSelect = document.getElementById('subject_id');
        const groupSelect = document.getElementById('group_id');
        
        if (subjectSelect) {
            subjectSelect.value = '';
            subjectSelect.required = true;
        }
        
        if (groupSelect) {
            groupSelect.value = '';
            groupSelect.required = true;
        }
        
        // Скрываем блок с информацией
        const infoBlock = document.querySelector('.assignment-create__info');
        if (infoBlock) {
            infoBlock.style.display = 'none';
        }
    };
});