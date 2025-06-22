@component('mail::message')
# Добро пожаловать!

Вам были созданы учётные данные для входа в систему:

**Логин:** {{ $login }}
**Пароль:** {{ $password }}

Пожалуйста, сохраните эти данные в надёжном месте.

@component('mail::button', ['url' => 'https://ваш-сайт.рф/login'])
Войти в систему
@endcomponent

@endcomponent