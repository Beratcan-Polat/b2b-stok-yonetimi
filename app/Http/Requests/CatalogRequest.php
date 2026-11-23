<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogRequest extends FormRequest
{
    protected $redirectRoute = 'katalog.index';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array{
     *     arama: list<string>,
     *     kategori: list<string>,
     *     page: list<string>
     * }
     */
    public function rules(): array
    {
        return [
            'arama' => ['nullable', 'string', 'max:100'],
            'kategori' => ['nullable', 'integer', 'exists:categories,id'],
            'siralama' => ['nullable', 'string', 'in:varsayilan,fiyat-artan,fiyat-azalan,tarih-eski,tarih-yeni,ad-az,ad-za'],
            'page' => ['nullable', 'integer', 'min:1'],

        ];
    }
}
