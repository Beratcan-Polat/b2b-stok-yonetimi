<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $rol = $this->route('rol');

        if ($rol instanceof Role) {
            return Gate::allows('update', $rol);
        }

        return Gate::allows('create', Role::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rol = $this->route('rol');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z][a-z0-9]*(?:-[a-z0-9]+)*$/',
                Rule::notIn(['super-admin']),
                Rule::unique('roles', 'name')->ignore($rol),
            ],

            'label' => [
                'required',
                'string',
                'max:255',
            ],

            'permission_ids' => [
                'sometimes',
                'array',
            ],

            'permission_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('permissions', 'id'),
            ],

            'is_super_admin' => [
                'prohibited',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Rol kodu zorunludur.',
            'name.string' => 'Rol kodu metin olmalıdır.',
            'name.max' => 'Rol kodu en fazla 100 karakter olabilir.',
            'name.regex' => 'Rol kodunda küçük İngilizce harfler, rakamlar ve aralarda tire kullanınız.',
            'name.not_in' => 'Bu rol kodu sistem tarafından ayrılmıştır.',
            'name.unique' => 'Bu rol kodu zaten kullanılıyor.',

            'label.required' => 'Rol adı zorunludur.',
            'label.string' => 'Rol adı metin olmalıdır.',
            'label.max' => 'Rol adı en fazla 255 karakter olabilir.',

            'permission_ids.array' => 'İzinler liste olarak gönderilmelidir.',
            'permission_ids.*.required' => 'İzin seçimi boş değer içeremez.',
            'permission_ids.*.integer' => 'Geçerli bir izin seçiniz.',
            'permission_ids.*.distinct' => 'Aynı izin birden fazla kez seçilemez.',
            'permission_ids.*.exists' => 'Seçilen izin bulunamadı.',

            'is_super_admin.prohibited' => 'Bu form üzerinden Süper Admin rolü oluşturulamaz.',
        ];
    }
}
