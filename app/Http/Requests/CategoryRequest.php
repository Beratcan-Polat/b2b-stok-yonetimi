<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $kategori = $this->route('kategori');

        return $kategori
            ? Gate::allows('update', $kategori)
            : Gate::allows('create', Category::class);
    }

    public function rules(): array
    {
        $kategori = $this->route('kategori');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($kategori),
            ],
            'name_en' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('panel.validation.category.name-required'),
            'name.string' => __('panel.validation.category.name-string'),
            'name.max' => __('panel.validation.category.name-max'),
            'name.unique' => __('panel.validation.category.name-unique'),
            'name_en.string' => __('panel.validation.category.name-en-string'),
            'name_en.max' => __('panel.validation.category.name-en-max'),
        ];
    }
}
