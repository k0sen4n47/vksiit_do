<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'required|min:15|max:50',
            'subject' => 'required|min:5|max:20',
            'name' => 'required',
            'email' => 'required|email'
        ];
    }
    public function attribute(){
        return[
            'name' => 'Имя'
        ];
    }
    public function messages(){
        return [
            'name.required' => 'Поле имя является обязательным',
            'email.required' => 'Поле email является обязательным',
            'subject.required' => 'Поле Тема сообщения является обязательным',
            'message.required' => 'Поле Сообщение является обязательным',
        ];
    }
    
}
