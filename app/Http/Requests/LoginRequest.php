<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('panel.validation.login.email-required'),
            'email.email' => __('panel.validation.login.email-email'),
            'password.required' => __('panel.validation.login.password-required'),
        ];
    }

    public function kimlikDogrula(): void
    {
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('panel.validation.login.email'),
            ]);
        }

        $this->session()->regenerate();
    }
}
