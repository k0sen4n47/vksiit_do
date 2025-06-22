@extends('admin.dashboard')

@section('admin_content')
<div class="dashboard__create-content">
    <h2>Список студентов</h2>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    {{-- Форма фильтрации --}}
    <div class="dashboard__filter">
        <form action="{{ route('admin.students.index') }}" method="GET" class="form-filter">
            <div class="form-group">
                <label for="search" class="sr-only">Поиск:</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Поиск по ФИО или email" value="{{ request('search') }}">
            </div>

            {{-- Фильтр по группе --}}
            <div class="form-group">
                <label for="group_id" class="mr-1">Группа:</label>
                <select name="group_id" id="group_id" class="form-control">
                    <option value="">Все группы</option>
                    @foreach ($groups as $group)
                    <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->short_name }}-{{ $group->course }}{{ $group->year }}@if(!empty($group->suffix)) {{ $group->suffix }}@endif</option>
                    @endforeach
                </select>
            </div>

            {{-- Фильтр по подгруппе --}}
            <div class="form-group">
                <label for="subgroup" class="mr-1">Подгруппа:</label>
                <select name="subgroup" id="subgroup" class="form-control">
                    <option value="">Все подгруппы</option>
                    <option value="first" {{ request('subgroup') == 'first' ? 'selected' : '' }}>Первая подгруппа</option>
                    <option value="second" {{ request('subgroup') == 'second' ? 'selected' : '' }}>Вторая подгруппа</option>
                    {{-- Можно добавить опцию для студентов без подгруппы, если необходимо --}}
                    {{-- <option value="none" {{ request('subgroup') == 'none' ? 'selected' : '' }}>Без подгруппы</option> --}}
                </select>
            </div>

            <div class="form-filter__buttons">
                <button type="submit" class="filter-confirm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="40px" height="40px">
                        <path fill="#bae0bd" d="M1.707 22.199L4.486 19.42 13.362 28.297 35.514 6.145 38.293 8.924 13.362 33.855z"/>
                        <path fill="#5e9c76" d="M35.514,6.852l2.072,2.072L13.363,33.148L2.414,22.199l2.072-2.072l8.169,8.169l0.707,0.707 l0.707-0.707L35.514,6.852 M35.514,5.438L13.363,27.59l-8.876-8.876L1,22.199l12.363,12.363L39,8.924L35.514,5.438L35.514,5.438z"/>
                    </svg>
                </button>
                <a href="{{ route('admin.students.index') }}" class="filter-unset">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M20 20L4 4m16 0L4 20"/>
                    </svg>
                </a>
            </div>
        </form>
    </div>

    @if ($students->isEmpty())
    <p>Студенты пока не созданы или не найдены по вашему фильтру.</p>
    @else
    <div class="student-list admin-list"> {{-- Контейнер для списка студентов --}}
        @foreach ($students as $student)
        <div class="student-list__item admin-list__item"> {{-- Элемент списка для каждого студента --}}
            <div class="student-list__info admin-list__info"> {{-- Блок с информацией о студенте --}}
                <div class="student-list__fio admin-list__name">{{ $student->fio }}</div>
                <div class="student-list__email">{{ $student->email }}</div>
                {{-- Выводим сгенерированный логин --}}
                <div class="student-list__login">Логин: {{ $student->login }}</div>
                {{-- Правильно выводим короткое название группы --}}
                <div class="student-list__group">Группа: {{ $student->group ? $student->group->short_name : 'Не назначена' }}</div>
                {{-- Отображение подгруппы --}}
                @if($student->subgroup)
                <div class="student-list__subgroup">Подгруппа: {{ $student->subgroup == 'first' ? 'Первая' : 'Вторая' }}</div>
                @endif
            </div>
            <div class="student-list__actions admin-list__actions"> {{-- Блок с действиями --}}
                <a href="{{ route('admin.students.edit', $student) }}" class="btn editor">Редактировать</a>
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn deletor" onclick="return confirm('Вы уверены, что хотите удалить этого студента?');">Удалить</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    @endif
</div>
@endsection