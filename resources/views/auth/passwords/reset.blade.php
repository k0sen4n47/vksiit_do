@extends('layouts.app')

@section('content')
<div class="forgot-password-container" style="max-width: 400px; margin: 40px auto; background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 24px rgba(39,24,198,0.07); border: 2px solid #2718c6;">
    <h2 class="block-title">Сброс пароля</h2>
    <p>
        Пароль должен содержать минимум 8 символов, хотя бы одну букву и одну цифру.
    </p>
    <form method="POST" action="{{ url('/password/reset') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="form-group">
            <label for="password">Новый пароль</label>
            <input id="password" type="password" name="password" class="form-control" required autofocus>
        </div>
        <div class="form-group">
            <label for="password-confirm">Подтвердите пароль</label>
            <input id="password-confirm" type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Сбросить пароль</button>
    </form>
    @if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
    @endif
</div>
@endsection