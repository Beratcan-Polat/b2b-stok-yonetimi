<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfilRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && ! $this->session()->has('impersonator_id');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Mevcut şifre alanı zorunludur.',
            'current_password.string' => 'Mevcut şifre geçerli bir metin olmalıdır.',
            'current_password.current_password' => 'Mevcut şifre yanlış.',
            'password.required' => 'Yeni şifre alanı zorunludur.',
            'password.string' => 'Yeni şifre geçerli bir metin olmalıdır.',
            'password.min' => 'Yeni şifre en az :min karakter olmalıdır.',
            'password.confirmed' => 'Yeni şifre ve onay alanı eşleşmiyor.',
        ];
    }
}
