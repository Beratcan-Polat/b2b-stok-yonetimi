<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;

class OrderController extends Controller
{
    public function index()
    {
        $siparisler = Order::with('product')->latest()->paginate(10);

        return view('siparisler.index', compact('siparisler'));
    }

    public function create(Product $urun)
    {
        if($urun->stock <=0)
            return redirect()->route('urunler.index')->with('error', 'Bu ürün stokta bulunmadığı için sipariş verilemez.');

        return view('siparisler.create', compact('urun'));
    }

    public function store(Request $request, Product $urun)
    {
        if($urun->stock <=0)
            return redirect()->route('urunler.index')->with('error', 'Bu ürün stokta bulunmadığı için sipariş verilemez.');

        $veriler = $request->validate(
            [
                'customer_name' => 'required|string|max:255',
                'quantity' => 'required|integer|min:1'
            ],
            [
                'customer_name.required' => 'Müşteri adı zorunludur.',
                'customer_name.string' => 'Müşteri adı metin olmalıdır.',
                'customer_name.max' => 'Müşteri adı en fazla 255 karakter olabilir.',
                'quantity.required' => 'Sipariş adedi zorunludur.',
                'quantity.integer' => 'Sipariş adedi tam sayı olmalıdır.',
                'quantity.min' => 'Sipariş adedi en az 1 olmalıdır.'
            ]
        );

        $adet = $veriler['quantity'];

        if ($adet > $urun->stock)
            {
                return back()->withErrors([
                    'quantity' => 'Sipariş adedi mevcut stoktan fazla olamaz.'
                ])->withInput();
            }

        $toplamTutar = $adet * $urun->price;

        Order::create([
            'product_id' => $urun->id,
            'customer_name' => $veriler['customer_name'],
            'quantity' => $adet,
            'total_price' => $toplamTutar,
            'status' => 'Bekliyor',
        ]);

        $urun->decrement('stock', $adet);

        return redirect()->route('siparisler.index')->with('success', 'Sipariş başarıyla oluşturuldu ve ürün stoğu güncellendi.');
    }
}
