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
            'name.required' => __('panel.validation.user.name-required'),
            'name.max' => __('panel.validation.user.name-max'),
            'email.required' => __('panel.validation.user.email-required'),
            'email.email' => __('panel.validation.user.email-email'),
            'email.unique' => __('panel.validation.user.email-unique'),
            'password.required' => __('panel.validation.user.password-required'),
            'password.min' => __('panel.validation.user.password-min'),
            'password.confirmed' => __('panel.validation.user.password-confirmed'),
            'password.prohibited' => __('panel.validation.user.password-prohibited'),
            'password_confirmation.prohibited' => __('panel.validation.user.password_confirmation-prohibited'),
            'role_id.required' => __('panel.validation.user.role_id-required'),
            'role_id.exists' => __('panel.validation.user.role_id-exists'),
            'role_id.integer' => __('panel.validation.user.role_id-integer'),
            'role_mode.in' => __('panel.validation.user.role_mode-in'),
            'new_role_name.required' => __('panel.validation.user.new_role_name-required'),
            'new_role_name.max' => __('panel.validation.user.new_role_name-max'),
            'new_role_slug.required' => __('panel.validation.user.new_role_slug-required'),
            'new_role_slug.unique' => __('panel.validation.user.new_role_slug-unique'),
            'new_role_slug.max' => __('panel.validation.user.new_role_slug-max'),
            'new_role_slug.not_in' => __('panel.validation.user.new_role_slug-not_in'),
            'new_role_permission_ids.prohibited' => __('panel.validation.user.new_role_permission_ids-prohibited'),
            'new_role_permission_ids.array' => __('panel.validation.user.new_role_permission_ids-array'),
            'new_role_permission_ids.*.integer' => __('panel.validation.user.new_role_permission_ids-item-integer'),
            'new_role_permission_ids.*.distinct' => __('panel.validation.user.new_role_permission_ids-item-distinct'),
            'new_role_permission_ids.*.exists' => __('panel.validation.user.new_role_permission_ids-item-exists'),
            'user_permissions_present.prohibited' => __('panel.validation.user.user_permissions_present-prohibited'),
            'user_permissions_present.accepted' => __('panel.validation.user.user_permissions_present-accepted'),
            'user_permission_ids.prohibited' => __('panel.validation.user.user_permission_ids-prohibited'),
            'user_permission_ids.array' => __('panel.validation.user.user_permission_ids-array'),
            'user_permission_ids.*.integer' => __('panel.validation.user.user_permission_ids-item-integer'),
            'user_permission_ids.*.distinct' => __('panel.validation.user.user_permission_ids-item-distinct'),
            'user_permission_ids.*.exists' => __('panel.validation.user.user_permission_ids-item-exists'),
            'permission_overrides.prohibited' => __('panel.validation.user.permission_overrides-prohibited'),
            'permission_overrides.array' => __('panel.validation.user.permission_overrides-array'),
            'permission_overrides.*.array' => __('panel.validation.user.permission_overrides-item-array'),
            'permission_overrides.*.permission_id.required' => __('panel.validation.user.permission_overrides-item-permission_id-required'),
            'permission_overrides.*.permission_id.integer' => __('panel.validation.user.permission_overrides-item-permission_id-integer'),
            'permission_overrides.*.permission_id.distinct' => __('panel.validation.user.permission_overrides-item-permission_id-distinct'),
            'permission_overrides.*.permission_id.exists' => __('panel.validation.user.permission_overrides-item-permission_id-exists'),
            'permission_overrides.*.state.required' => __('panel.validation.user.permission_overrides-item-state-required'),
            'permission_overrides.*.state.in' => __('panel.validation.user.permission_overrides-item-state-in'),
        ];
    }
}
