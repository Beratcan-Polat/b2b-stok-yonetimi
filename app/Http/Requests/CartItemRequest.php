<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity' => 'required | integer | min:1 | max:10000'
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.required' => __('sepet.invalid_quantity'),
            'quantity.integer' => __('sepet.invalid_quantity'),
            'quantity.min' => __('sepet.invalid_quantity'),
            'quantity.max' => __('sepet.invalid_quantity'),
        ];
    }
}
