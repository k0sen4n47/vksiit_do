console.log('student-assignment.js loaded');
document.addEventListener('DOMContentLoaded', function () {
    console.log('student-assignment.js loaded');
    
    // --- AJAX обработчик для формы ответа ---
    const finalAnswerForm = document.getElementById('finalAnswerForm');
    const editAnswerForm = document.getElementById('editAnswerForm');
    
    function handleAnswerFormSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        // Показываем индикатор загрузки
        submitButton.disabled = true;
        submitButton.textContent = 'Отправка...';
        
        // Собираем данные формы
        const formData = new FormData(form);
        
        // Отправляем данные через AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                return response.json().then(errorData => {
                    console.error('Server error:', errorData);
                    throw new Error(errorData.message || 'Network response was not ok');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Success response:', data);
            
            if (data.success) {
                // Показываем сообщение об успехе
                showSuccessMessage(data.message || 'Ответ успешно отправлен!');
                
                // Обновляем страницу через небольшую задержку
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Произошла ошибка при отправке ответа');
            }
        })
        .catch(error => {
            console.error('Error details:', error);
            
            // Восстанавливаем кнопку
            submitButton.disabled = false;
            submitButton.textContent = originalText;
            
            // Показываем ошибку пользователю
            showErrorMessage(error.message || 'Произошла ошибка при отправке ответа');
        });
    }
    
    // Функция для показа сообщений
    function showSuccessMessage(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success';
        alertDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;';
        alertDiv.textContent = message;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
    
    function showErrorMessage(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger';
        alertDiv.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;';
        alertDiv.textContent = message;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
    
    // Привязываем обработчики к формам
    if (finalAnswerForm) {
        finalAnswerForm.addEventListener('submit', handleAnswerFormSubmit);
    }
    
    if (editAnswerForm) {
        editAnswerForm.addEventListener('submit', handleAnswerFormSubmit);
    }
    
    const pagesContainer = document.getElementById('assignment-pages');
    const pages = Array.from(pagesContainer.querySelectorAll('.student-assignment-view__page'));
    const totalPages = pages.length;
    let currentPage = 0;

    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');
    const pageIndicator = document.getElementById('current-page');

    function updateProgressBar(index) {
        const progressBar = document.getElementById('progress-bar-inner');
        const total = totalPages;
        if (progressBar && total > 1) {
            const percent = ((index + 1) / total) * 100;
            progressBar.style.width = percent + '%';
        } else if (progressBar) {
            progressBar.style.width = '100%';
        }
    }

    function initStudentTinyMCE() {
        if (typeof tinymce !== 'undefined') {
            tinymce.remove();
            tinymce.init({
                selector: 'textarea.tinymce-student',
                language: 'ru',
                language_url: '/js/tinymce/langs/ru.js',
                menubar: false,
                plugins: 'lists link image table',
                toolbar: 'undo redo | bold italic underline | bullist numlist | link image table',
                height: 250,
                license_key: 'gpl',
            });
        }
    }

    function showPage(index) {
        pages.forEach((page, i) => {
            page.style.display = (i === index) ? 'block' : 'none';
        });
        if (pageIndicator) {
            pageIndicator.textContent = index + 1;
        }
        updateProgressBar(index);
        // Кнопка "Назад"
        if (index === 0) {
            if (prevBtn) {
                prevBtn.classList.add('btn-cabinet--disabled');
            }
        } else {
            if (prevBtn) {
                prevBtn.classList.remove('btn-cabinet--disabled');
            }
        }
        // Кнопка "Вперёд"
        if (index === totalPages - 1) {
            if (nextBtn) {
                nextBtn.classList.add('btn-cabinet--disabled');
            }
        } else {
            if (nextBtn) {
                nextBtn.classList.remove('btn-cabinet--disabled');
            }
        }
        // Инициализация редакторов кода на текущей странице
        // initCodeEditors(pages[index]);
        // Инициализация TinyMCE для текстовых ответов
        initStudentTinyMCE();
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            console.log('prev clicked');
            if (currentPage > 0) {
                currentPage--;
                showPage(currentPage);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            console.log('next clicked');
            if (currentPage < totalPages - 1) {
                currentPage++;
                showPage(currentPage);
            }
        });
    }

    // Инициализация
    showPage(currentPage);
    // Инициализация TinyMCE при первой загрузке
    initStudentTinyMCE();
    
    // --- CodeMirror и предпросмотр ---
    // function initCodeEditors(page) {
    //     ...
    // }
    // pages.forEach(function(page) {
    //     initCodeEditors(page);
    // });
}); 