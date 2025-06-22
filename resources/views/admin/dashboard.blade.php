@extends('layouts.app')

@section('title-page')
Панель администратора
@endsection

@section('content')
<div class="admin-dashboard">
    <div class="admin-dashboard__sidebar">
        {{-- Сюда будет включена боковая панель навигации админа --}}
        @include('admin.inc.sidebar')
    </div>
    <div class="admin-dashboard__content">
        <h1 class="admin-dashboard__title">Добро пожаловать, Администратор!</h1>
        {{-- Здесь будет отображаться контент конкретной страницы (например, список групп, форма создания и т.д.) --}}
        @yield('admin_content')
    </div>
</div>
@endsection 