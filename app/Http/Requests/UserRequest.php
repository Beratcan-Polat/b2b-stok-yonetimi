<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $kullanici = $this->route('kullanici');

        return $kullanici
            ? Gate::allows('update', $kullanici)
            : Gate::allows('create', User::class);
    }

    public function rules(): array
    {
        $kullanici = $this->route('kullanici');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($kullanici),
            ],
            'password' => [
                $kullanici ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role_id' => 'required|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ad soyad zorunludur.',
            'name.max' => 'Ad soyad en fazla 255 karakter olabilir.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.unique' => 'Bu e-posta adresi daha önce kullanılmış.',
            'password.required' => 'Parola zorunludur.',
            'password.min' => 'Parola en az 8 karakter olmalıdır.',
            'password.confirmed' => 'Parola tekrarı eşleşmiyor.',
            'role_id.required' => 'Rol seçimi zorunludur.',
            'role_id.exists' => 'Seçilen rol bulunamadı.',
        ];
    }
}
