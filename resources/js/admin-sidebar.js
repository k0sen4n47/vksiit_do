document.addEventListener('DOMContentLoaded', function() {
    const submenuLinks = document.querySelectorAll('.admin-sidebar__menu-item.has-submenu > .admin-sidebar__menu-link');

    submenuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Предотвращаем переход по ссылке #
            const parentMenuItem = this.closest('.admin-sidebar__menu-item');
            parentMenuItem.classList.toggle('active');
        });
    });

    // Optionally, close other open submenus when one is opened
    // Or, keep them open if the user desires multiple open submenus
}); 