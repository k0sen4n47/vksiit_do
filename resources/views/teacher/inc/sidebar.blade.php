<aside class="teacher-sidebar">
    <ul class="teacher-sidebar__menu">
        {{-- Главная --}}
        <li class="teacher-sidebar__menu-item">
            <a href="{{ route('teacher.dashboard') }}" class="teacher-sidebar__menu-link @if(request()->routeIs('teacher.dashboard')) active @endif">Главная</a>
        </li>

        {{-- Задания --}}
        <li class="teacher-sidebar__menu-item">
            <a href="{{ route('teacher.assignments.index') }}" class="teacher-sidebar__menu-link @if(request()->routeIs('teacher.assignments.index')) active @endif">Мои задания</a>
        </li>

        {{-- Добавить задание --}}
        <li class="teacher-sidebar__menu-item">
            <a href="{{ route('teacher.assignments.subjects.index') }}" class="teacher-sidebar__menu-link @if(request()->routeIs('teacher.assignments.subjects.*')) active @endif">Добавить задание</a>
        </li>

        {{-- Добавьте другие пункты меню для преподавателя здесь --}}
    </ul>
</aside> 