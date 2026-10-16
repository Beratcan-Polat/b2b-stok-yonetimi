<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (! $this->route('kullanici')) {
            $this->merge(['role_mode' => $this->input('role_mode', 'existing')]);

            if ($this->input('role_mode') === 'new' && is_string($this->input('new_role_name'))) {
                $this->merge(['new_role_slug' => Str::slug($this->input('new_role_name'))]);
            }
        }
    }

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
        $yeniRol = ! $kullanici && $this->input('role_mode') === 'new';
        $superAdmin = $this->user()->isSuperAdmin();

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($kullanici),
            ],
            'password' => $kullanici && ! $superAdmin
                ? ['prohibited']
                : [$kullanici ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => $kullanici && ! $superAdmin ? ['prohibited'] : ['nullable', 'string'],
            'role_id' => [
                $yeniRol ? 'prohibited' : 'required',
                'integer',
                Rule::exists('roles', 'id')->where(function ($query): void {
                    if (! $this->user()->isSuperAdmin()) {
                        $query->where('is_super_admin', false);
                    }
                }),
            ],
            'role_mode' => $kullanici ? ['prohibited'] : ['required', Rule::in($superAdmin ? ['existing', 'new'] : ['existing'])],
            'new_role_name' => $yeniRol && $superAdmin ? ['required', 'string', 'max:255'] : ['prohibited'],
            'new_role_slug' => $yeniRol && $superAdmin
                ? ['required', 'string', 'max:100', Rule::notIn(['super-admin']), Rule::unique('roles', 'name')]
                : ['prohibited'],
            'new_role_permission_ids' => $yeniRol && $superAdmin ? ['sometimes', 'array'] : ['prohibited'],
            'new_role_permission_ids.*' => ['integer', 'distinct', Rule::exists('permissions', 'id')],
            'permission_overrides' => $this->user()->isSuperAdmin()
                && ! $yeniRol ? ['sometimes', 'array']
                : ['prohibited'],
            'user_permissions_present' => $superAdmin && ! $yeniRol ? ['sometimes', 'accepted'] : ['prohibited'],
            'user_permission_ids' => $superAdmin && ! $yeniRol ? ['sometimes', 'array'] : ['prohibited'],
            'user_permission_ids.*' => ['integer', 'distinct', Rule::exists('permissions', 'id')],
            'permission_overrides.*' => ['array:permission_id,state'],
            'permission_overrides.*.permission_id' => [
                'required', 'integer', 'distinct', Rule::exists('permissions', 'id'),
            ],
            'permission_overrides.*.state' => [
                'required', Rule::in(['inherit', 'allow', 'deny']),
            ],
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
            'password.prohibited' => 'Başka bir kullanıcının parolasını yalnızca Süper Admin değiştirebilir.',
            'password_confirmation.prohibited' => 'Başka bir kullanıcının parolasını yalnızca Süper Admin değiştirebilir.',
            'role_id.required' => 'Rol seçimi zorunludur.',
            'role_id.exists' => 'Seçilen rol bulunamadı veya bu rolü atama yetkiniz yok.',
            'role_id.integer' => 'Geçerli bir rol seçiniz.',
            'role_mode.in' => 'Yeni rol oluşturma yetkiniz yok veya geçersiz bir seçenek seçtiniz.',
            'new_role_name.required' => 'Yeni rol adı zorunludur.',
            'new_role_name.max' => 'Rol adı en fazla 255 karakter olabilir.',
            'new_role_slug.required' => 'Bu rol adından geçerli bir rol kodu oluşturulamadı.',
            'new_role_slug.unique' => 'Bu adla oluşturulan rol kodu zaten kullanılıyor.',
            'new_role_slug.max' => 'Rol kodu en fazla 100 karakter olabilir.',
            'new_role_slug.not_in' => 'Süper Admin rol kodu kullanılamaz.',
            'new_role_permission_ids.prohibited' => 'Yeni rol yetkilerini yalnızca Süper Admin belirleyebilir.',
            'new_role_permission_ids.array' => 'Rol yetkileri liste olarak gönderilmelidir.',
            'new_role_permission_ids.*.integer' => 'Geçerli bir rol yetkisi seçiniz.',
            'new_role_permission_ids.*.distinct' => 'Aynı rol yetkisi birden fazla kez seçilemez.',
            'new_role_permission_ids.*.exists' => 'Seçilen rol yetkisi bulunamadı.',
            'user_permissions_present.prohibited' => 'Kullanıcıya özel yetkileri yalnızca Süper Admin değiştirebilir.',
            'user_permissions_present.accepted' => 'Geçerli bir yetki seçimi gönderiniz.',
            'user_permission_ids.prohibited' => 'Kullanıcıya özel yetkileri yalnızca Süper Admin değiştirebilir.',
            'user_permission_ids.array' => 'Kullanıcı yetkileri liste olarak gönderilmelidir.',
            'user_permission_ids.*.integer' => 'Geçerli bir izin seçiniz.',
            'user_permission_ids.*.distinct' => 'Aynı izin birden fazla kez gönderilemez.',
            'user_permission_ids.*.exists' => 'Seçilen izin bulunamadı.',
            'permission_overrides.prohibited' => 'Kullanıcıya özel yetkileri yalnızca Süper Admin değiştirebilir.',
            'permission_overrides.array' => 'Kullanıcı yetkileri liste olarak gönderilmelidir.',
            'permission_overrides.*.array' => 'Geçerli bir kullanıcı yetkisi gönderiniz.',
            'permission_overrides.*.permission_id.required' => 'İzin seçimi zorunludur.',
            'permission_overrides.*.permission_id.integer' => 'Geçerli bir izin seçiniz.',
            'permission_overrides.*.permission_id.distinct' => 'Aynı izin birden fazla kez gönderilemez.',
            'permission_overrides.*.permission_id.exists' => 'Seçilen izin bulunamadı.',
            'permission_overrides.*.state.required' => 'Yetki durumunu seçiniz.',
            'permission_overrides.*.state.in' => 'Geçersiz yetki durumu.',
        ];
    }
}
