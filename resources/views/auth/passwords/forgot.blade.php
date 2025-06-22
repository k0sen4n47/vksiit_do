@extends('layouts.app')

@section('content')
<div class="password-reset">
    <div class="logo">
        <img src="/images/logotypes/logo.svg" alt="Logo">
    </div>
    <h2 class="title-form">Забыли пароль</h2>
    <p class="p2">Для сброса пароля укажите ваш E-mail или логин и адрес электронной почты. Если ваша учетная запись существует в базе данных, на ваш адрес электронной почты будет отправлено письмо, содержащее инструкции по восстановлению доступа.</p>
    <form class="password-reset-form" method="POST" action="">
        @csrf
        <div class="form-floating">
            <input type="text" name="login" class="form-control input-field" id="inputLogin" placeholder="Логин" />
            <label for="inputLogin" class="label-input">Логин *</label>
        </div>
        <div class="form-floating">
            <input type="email" name="email" class="form-control input-field" id="inputEmail" placeholder="Почта" />
            <label for="inputEmail" class="label-input">Почта *</label>
        </div>
        <button type="submit" formaction="{{ route('password.forgot.login') }}" class="btn">Найти</button>
        <button type="submit" formaction="{{ route('password.email') }}" class="btn">Найти</button>
    </form>
    <a href="{{ route('login') }}" class="btn empty exit">Выход</a>
    @if (session('status'))
        <div class="alert alert-success forgot-password-alert" id="reset-success-alert">
            {{ session('status') == 'We have emailed your password reset link.' ? 'Ссылка для сброса пароля отправлена на вашу почту.' : session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger forgot-password-alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('reset-success-alert');
        if (alert) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 4000);
        }
    });
</script>
@endsection 