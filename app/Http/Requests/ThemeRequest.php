<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ThemeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('tema.yonet') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array{theme: list<string|\Illuminate\Validation\Rules\In>}
     */
    public function rules(): array
    {
        return [
            'theme' => [
                'required',
                'string',

                Rule::in(array_keys(config('themes.options', []))),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'theme.required' => __('tema.required'),
            'theme.string' => __('tema.invalid'),
            'theme.in' => __('tema.invalid'),
        ];
    }
}
