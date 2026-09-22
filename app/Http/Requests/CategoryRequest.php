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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Kategori adı zorunludur.',
            'name.string' => 'Kategori adı metin olmalıdır.',
            'name.max' => 'Kategori en fazla 255 karakter olabilir.',
            'name.unique' => 'Bu kategori adı daha önce kullanıldı.',
        ];
    }
}
