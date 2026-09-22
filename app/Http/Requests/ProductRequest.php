<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $urun = $this->route('urun');

        return $urun
            ? Gate::allows('update', $urun)
            : Gate::allows('create', Product::class);
    }

    public function rules(): array
    {
        $urun = $this->route('urun');

        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($urun),
            ],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori seçimi zorunludur.',
            'category_id.exists' => 'Seçilen kategori bulunamadı.',
            'name.required' => 'Ürün adı zorunludur.',
            'name.max' => 'Ürün adı en fazla 255 karakter olabilir.',
            'sku.required' => 'SKU bilgisi zorunludur.',
            'sku.max' => 'SKU en fazla 100 karakter olabilir.',
            'sku.unique' => 'Bu SKU daha önce kullanılmış.',
            'price.required' => 'Ürün fiyatı zorunludur.',
            'price.numeric' => 'Ürün fiyatı sayısal olmalıdır.',
            'price.min' => 'Ürün fiyatı negatif olamaz.',
            'stock.required' => 'Stok adedi zorunludur.',
            'stock.integer' => 'Stok adedi tam sayı olmalıdır.',
            'stock.min' => 'Stok adedi negatif olamaz.',
            'image.image' => 'Yüklenen dosya bir görsel olmalıdır.',
            'image.mimes' => 'Görsel JPEG, PNG, JPG veya WEBP formatında olmalıdır.',
            'image.max' => 'Görsel en fazla 2 MB olabilir.',
        ];
    }
}
