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
            'customer_name.required' => 'Müşteri adı zorunludur.',
            'customer_name.string' => 'Müşteri adı metin olmalıdır.',
            'customer_name.max' => 'Müşteri adı en fazla 255 karakter olabilir.',
            'quantity.required' => 'Sipariş adedi zorunludur.',
            'quantity.integer' => 'Sipariş adedi tam sayı olmalıdır.',
            'quantity.min' => 'Sipariş adedi en az 1 olmalıdır.',
        ];
    }
}
