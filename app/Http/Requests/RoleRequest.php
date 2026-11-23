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
            'name.required' => __('panel.validation.role.name-required'),
            'name.string' => __('panel.validation.role.name-string'),
            'name.max' => __('panel.validation.role.name-max'),
            'name.regex' => __('panel.validation.role.name-regex'),
            'name.not_in' => __('panel.validation.role.name-not_in'),
            'name.unique' => __('panel.validation.role.name-unique'),

            'label.required' => __('panel.validation.role.label-required'),
            'label.string' => __('panel.validation.role.label-string'),
            'label.max' => __('panel.validation.role.label-max'),

            'permission_ids.array' => __('panel.validation.role.permission_ids-array'),
            'permission_ids.*.required' => __('panel.validation.role.permission_ids-item-required'),
            'permission_ids.*.integer' => __('panel.validation.role.permission_ids-item-integer'),
            'permission_ids.*.distinct' => __('panel.validation.role.permission_ids-item-distinct'),
            'permission_ids.*.exists' => __('panel.validation.role.permission_ids-item-exists'),

            'is_super_admin.prohibited' => __('panel.validation.role.is_super_admin-prohibited'),
        ];
    }
}
