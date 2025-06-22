<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $login;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Ваши данные для входа')
            ->markdown('emails.send_credentials')
            ->with([
                'login' => $this->login,
                'password' => $this->password,
            ]);
    }
} 