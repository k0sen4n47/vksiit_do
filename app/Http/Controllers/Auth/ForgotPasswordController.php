<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * Отправка ссылки для сброса пароля по логину.
     */
    public function sendResetLinkByLogin(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
        ]);

        $user = User::where('login', $request->login)->first();
        if (!$user) {
            // Не раскрываем, что пользователя нет
            return back()->with('status', 'Если пользователь найден, на почту отправлено письмо с инструкцией.');
        }

        // Используем стандартный Password Broker для отправки письма
        $status = Password::sendResetLink(['email' => $user->email]);

        return back()->with('status', __($status));
    }
} 