@component('mail::message')
<div style="text-align: center;">
    <img src="{{ $logo }}" alt="Логотип" style="height: 60px; margin-bottom: 20px;">
</div>
# Здравствуйте!

Вы получили это письмо, потому что для вашей учетной записи был запрошен сброс пароля.

@component('mail::button', ['url' => $url])
Сбросить пароль
@endcomponent

Срок действия ссылки для сброса пароля истекает через 60 минут.

Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.

С уважением,  
ВКСиИТ
@endcomponent 