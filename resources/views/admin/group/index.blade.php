@extends('admin.dashboard')

@section('admin_content')

<div class="dashboard__create-content">
    <h3>Список групп</h3>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="dashboard__filter">
        {{-- Форма фильтрации --}}
        <form action="{{ route('admin.groups.index') }}" method="GET" class="form-filter">
            <div class="form-group">
                <label for="search" class="sr-only">Поиск:</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Поиск по названию или аббревиатуре" value="{{ request('search') }}">
            </div>

            <div class="form-group">
                <label for="course" class="mr-1">Курс:</label>
                <select name="course" id="course" class="form-control">
                    <option value="">Все курсы</option>
                    @for ($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}" {{ request('course') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                </select>
            </div>

            <div class="form-group">
                <label for="suffix_status" class="mr-1">Суффикс:</label>
                <select name="suffix_status" id="suffix_status" class="form-control">
                    <option value="">Все</option>
                    <option value="with_suffix" {{ request('suffix_status') == 'with_suffix' ? 'selected' : '' }}>С суффиксом</option>
                    <option value="without_suffix" {{ request('suffix_status') == 'without_suffix' ? 'selected' : '' }}>Без суффикса</option>
                </select>
            </div>
            <div class="form-filter__buttons">
                <button type="submit" class="filter-confirm"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" width="40px" height="40px">
                        <path fill="#bae0bd" d="M1.707 22.199L4.486 19.42 13.362 28.297 35.514 6.145 38.293 8.924 13.362 33.855z" />
                        <path fill="#5e9c76" d="M35.514,6.852l2.072,2.072L13.363,33.148L2.414,22.199l2.072-2.072l8.169,8.169l0.707,0.707 l0.707-0.707L35.514,6.852 M35.514,5.438L13.363,27.59l-8.876-8.876L1,22.199l12.363,12.363L39,8.924L35.514,5.438L35.514,5.438z" />
                    </svg></button>
                <a href="{{ route('admin.groups.index') }}" class="filter-unset"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M20 20L4 4m16 0L4 20" />
                    </svg></a>
            </div>
        </form>
    </div>

    @if ($groups->isEmpty())
    <p>Группы пока не созданы или не найдены по вашему фильтру.</p>
    @else
    <div class="group-list admin-list"> {{-- Контейнер для списка групп --}}
        @foreach ($groups as $group)
        <div class="group-list__item admin-list__item"> {{-- Элемент списка для каждой группы --}}
            <div class="group-list__info admin-list__info"> {{-- Блок с информацией о группе --}}
                <div class="group-list__short-name admin-list__short-name">{{ $group->short_name }}</div>
                <div class="group-list__curator admin-list__name">Куратор: {{ $group->curator ? $group->curator->fio : 'Не назначен' }}</div>
            </div>
            <div class="group-list__actions admin-list__actions"> {{-- Блок с действиями --}}
                <a href="{{ route('admin.groups.edit', $group) }}" class="btn editor">Редактировать</a>
                <form action="{{ route('admin.groups.destroy', $group) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn deletor" onclick="return confirm('Вы уверены, что хотите удалить эту группу?');">Удалить</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    @endif
</div>
@endsection