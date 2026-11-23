<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Order::class);
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => __('panel.validation.order.customer_name-required'),
            'customer_name.string' => __('panel.validation.order.customer_name-string'),
            'customer_name.max' => __('panel.validation.order.customer_name-max'),
            'quantity.required' => __('panel.validation.order.quantity-required'),
            'quantity.integer' => __('panel.validation.order.quantity-integer'),
            'quantity.min' => __('panel.validation.order.quantity-min'),
        ];
    }
}
