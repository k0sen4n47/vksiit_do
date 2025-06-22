@extends('admin.dashboard')

@section('admin_content')
<div class="dashboard__create-content">
    <h2>Список преподавателей</h2>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="dashboard__filter">
        {{-- Форма фильтрации --}}
        <form action="{{ route('admin.teachers.index') }}" method="GET" class="form-filter">
            <div class="form-group">
                <label for="search" class="sr-only">Поиск:</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Поиск по ФИО или Email" value="{{ request('search') }}">
            </div>

            <div class="form-filter__buttons">
                <button type="submit" class="filter-confirm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="40px" height="40px">
                        <path fill="#bae0bd" d="M1.707 22.199L4.486 19.42 13.362 28.297 35.514 6.145 38.293 8.924 13.362 33.855z" />
                        <path fill="#5e9c76" d="M35.514,6.852l2.072,2.072L13.363,33.148L2.414,22.199l2.072-2.072l8.169,8.169l0.707,0.707 l0.707-0.707L35.514,6.852 M35.514,5.438L13.363,27.59l-8.876-8.876L1,22.199l12.363,12.363L39,8.924L35.514,5.438L35.514,5.438z" />
                    </svg>
                </button>
                <a href="{{ route('admin.teachers.index') }}" class="filter-unset">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M20 20L4 4m16 0L4 20" />
                    </svg>
                </a>
            </div>
        </form>
    </div>

    @if ($teachers->isEmpty())
    <p>Преподаватели пока не созданы или не найдены по вашему фильтру.</p>
    @else
    <div class="teacher-list admin-list">
        @foreach ($teachers as $teacher)
        <div class="teacher-list__item admin-list__item">
            <div class="teacher-list__info admin-list__info">
                <div class="teacher-list__fio admin-list__name">ФИО: {{ $teacher->fio }}</div>
                <div class="teacher-list__email">Email: {{ $teacher->email }}</div>
                <div class="teacher-list__login">Логин: {{ $teacher->login }}</div>
            </div>
            <div class="teacher-list__actions admin-list__actions">
                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn editor">Редактировать</a>
                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn deletor" onclick="return confirm('Вы уверены, что хотите удалить этого преподавателя?');">Удалить</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection