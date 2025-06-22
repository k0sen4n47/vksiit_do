<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Восстановление пароля')
            ->greeting('Здравствуйте!')
            ->line('Вы получили это письмо, потому что для вашей учетной записи был запрошен сброс пароля.')
            ->action('Сбросить пароль', $url)
            ->line('Срок действия ссылки для сброса пароля истекает через 60 минут.')
            ->line('Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.')
            ->salutation('С уважением, ВКСиИТ')
            ->markdown('emails.reset_password', [
                'url' => $url,
                'logo' => asset('images/logotypes/logo.svg'),
            ]);
    }
} 