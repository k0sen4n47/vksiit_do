// Функция для очистки выбора предмета и группы
function clearSelection() {
    console.log('Clear selection called');
    
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

// Экспортируем функцию для использования в других модулях
export { clearSelection }; 