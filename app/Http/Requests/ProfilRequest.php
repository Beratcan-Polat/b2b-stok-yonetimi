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
            'current_password.required' => __('panel.validation.profil.current_password-required'),
            'current_password.string' => __('panel.validation.profil.current_password-string'),
            'current_password.current_password' => __('panel.validation.profil.current_password-current_password'),
            'password.required' => __('panel.validation.profil.password-required'),
            'password.string' => __('panel.validation.profil.password-string'),
            'password.min' => __('panel.validation.profil.password-min'),
            'password.confirmed' => __('panel.validation.profil.password-confirmed'),
        ];
    }
}
