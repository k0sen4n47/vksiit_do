<header>
    <div class="container">
        <div class="header-info">
            <div class="header-info__left">
                {{-- Ссылка на логотип --}}
                <a href="/" class="logo-link">
                    <img src="{{ asset('images/logotypes/mini-logo.png') }}" alt="" class="logo">
                </a>
                {{-- Навигация для всех пользователей (например, Главная) --}}
                <nav>
                    <a href="{{ route('welcome') }}">Главная</a>
                    @auth
                        @if(Auth::user()->role === 'teacher')
                            <a href="{{ route('teacher.assignments.index') }}">Мои задания</a>
                            <a href="{{ route('teacher.assignments.create') }}">Создать задание</a>
                        @elseif(Auth::user()->role === 'student')
                            <a href="{{ route('student.subjects.index') }}">Предметы</a>
                        @endif
                    @endauth
                </nav>
            </div>
            <div class="header-info__right">
                @guest
                {{-- Ссылки для неавторизованных пользователей (только Войти) --}}
                <a class="btn stroke" href="{{ route('login') }}">Войти</a>
                @endguest

                @auth
                {{-- Ссылки для авторизованных пользователей --}}
                @php
                    $dashboardRoute = '#';
                    if (Auth::check()) {
                        switch (Auth::user()->role) {
                            case 'admin':
                                $dashboardRoute = route('admin.dashboard');
                                break;
                            case 'teacher':
                                $dashboardRoute = route('teacher.dashboard');
                                break;
                            case 'student':
                                $dashboardRoute = route('student.dashboard');
                                break;
                        }
                    }
                @endphp
                <a class="btn stroke"  href="{{ $dashboardRoute }}">Личный кабинет</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn stroke" type="submit">Выйти</button>
                </form>
                @endauth
            </div>
        </div>
    </div>
</header>