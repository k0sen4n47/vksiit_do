<aside class="admin-sidebar">
    <ul class="admin-sidebar__menu">
        {{-- Главная --}}
        <li class="admin-sidebar__menu-item">
            <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__menu-link @if(request()->routeIs('admin.dashboard')) active @endif">Главная</a>
        </li>

        {{-- Группы --}}
        <li class="admin-sidebar__menu-item has-submenu @if(request()->routeIs('admin.groups.*') || request()->routeIs('admin.group-name-components.*')) active @endif">
            <a href="#" class="admin-sidebar__menu-link">Группы</a>
            <ul class="admin-sidebar__submenu">
                <li class="admin-sidebar__submenu-item">
                    <a href="{{ route('admin.groups.create') }}" class="admin-sidebar__submenu-link @if(request()->routeIs('admin.groups.create')) active @endif">Создать группу</a>
                </li>
                <li class="admin-sidebar__submenu-item">
                    <a href="{{ route('admin.groups.index') }}" class="admin-sidebar__submenu-link @if(request()->routeIs('admin.groups.index')) active @endif">Список групп</a>
                </li>
                 <li class="admin-sidebar__submenu-item">
                    <a href="{{ route('admin.group-name-components.index') }}" class="admin-sidebar__submenu-link @if(request()->routeIs('admin.group-name-components.index')) active @endif">Компоненты названий групп</a>
                </li>
                 <li class="admin-sidebar__submenu-item">
                    <a href="{{ route('admin.group-name-components.create') }}" class="admin-sidebar__submenu-link @if(request()->routeIs('admin.group-name-components.create')) active @endif">Создать компоненты</a>
                </li>
            </ul>
        </li>

        {{-- Студенты --}}
        <li class="admin-sidebar__menu-item has-submenu @if(request()->routeIs('admin.students.*')) active @endif">
            <a href="#" class="admin-sidebar__menu-link">Студенты</a>
            <ul class="admin-sidebar__submenu">
                <li class="admin-sidebar__submenu-item">
                    <a href="{{ route('admin.students.create') }}" class="admin-sidebar__submenu-link @if(request()->routeIs('admin.students.create')) active @endif">Создать студента</a>
                </li>
                <li class="admin-sidebar__submenu-item">
                    <a href="{{ route('admin.students.index') }}" class="admin-sidebar__submenu-link @if(request()->routeIs('admin.students.index')) active @endif">Список студентов</a>
                </li>
            </ul>
        </li>

        {{-- Преподаватели --}}
        <li class="admin-sidebar__menu-item has-submenu @if(request()->routeIs('admin.teachers.*')) active @endif">
            <a href="#" class="admin-sidebar__menu-link">Преподаватели</a>
            <ul class="admin-sidebar__submenu">
                <li class="admin-sidebar__submenu-item">
                    <a href="{{ route('admin.teachers.create') }}" class="admin-sidebar__submenu-link @if(request()->routeIs('admin.teachers.create')) active @endif">Создать преподавателя</a>
                </li>
                <li class="admin-sidebar__submenu-item">
                    <a href="{{ route('admin.teachers.index') }}" class="admin-sidebar__submenu-link @if(request()->routeIs('admin.teachers.index')) active @endif">Список преподавателей</a>
                </li>
            </ul>
        </li>

        {{-- Предметы --}}
        <li class="admin-sidebar__menu-item has-submenu @if(request()->routeIs('admin.subjects.*')) active @endif">
            <a href="#" class="admin-sidebar__menu-link">Предметы</a>
            <ul class="admin-sidebar__submenu">
                <li class="admin-sidebar__submenu-item">
                    <a href="{{ route('admin.subjects.create') }}" class="admin-sidebar__submenu-link @if(request()->routeIs('admin.subjects.create')) active @endif">Создать предмет</a>
                </li>
                <li class="admin-sidebar__submenu-item">
                    <a href="{{ route('admin.subjects.index') }}" class="admin-sidebar__submenu-link @if(request()->routeIs('admin.subjects.index')) active @endif">Список предметов</a>
                </li>
            </ul>
        </li>

    </ul>
</aside>