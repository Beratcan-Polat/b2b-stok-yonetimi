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
            'category_id.required' => __('panel.validation.product.category_id-required'),
            'category_id.exists' => __('panel.validation.product.category_id-exists'),
            'name.required' => __('panel.validation.product.name-required'),
            'name.max' => __('panel.validation.product.name-max'),
            'sku.required' => __('panel.validation.product.sku-required'),
            'sku.max' => __('panel.validation.product.sku-max'),
            'sku.unique' => __('panel.validation.product.sku-unique'),
            'price.required' => __('panel.validation.product.price-required'),
            'price.numeric' => __('panel.validation.product.price-numeric'),
            'price.min' => __('panel.validation.product.price-min'),
            'stock.required' => __('panel.validation.product.stock-required'),
            'stock.integer' => __('panel.validation.product.stock-integer'),
            'stock.min' => __('panel.validation.product.stock-min'),
            'image.image' => __('panel.validation.product.image-image'),
            'image.mimes' => __('panel.validation.product.image-mimes'),
            'image.max' => __('panel.validation.product.image-max'),
        ];
    }
}
