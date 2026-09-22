<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Order::class);

        $siparisler = Order::with('product')->latest()->paginate(10);

        return view('siparisler.index', compact('siparisler'));
    }

    public function create(Product $urun)
    {
        Gate::authorize('create', Order::class);

        if ($urun->stock <= 0) {
            return redirect()->route('urunler.index')->with('error', 'Bu ürün stokta bulunmadığı için sipariş verilemez.');
        }

        return view('siparisler.create', compact('urun'));
    }

    public function store(OrderRequest $request, Product $urun)
    {
        if ($urun->stock <= 0) {
            return redirect()->route('urunler.index')->with('error', 'Bu ürün stokta bulunmadığı için sipariş verilemez.');
        }

        $veriler = $request->validated();

        $adet = $veriler['quantity'];

        if ($adet > $urun->stock) {
            return back()->withErrors([
                'quantity' => 'Sipariş adedi mevcut stoktan fazla olamaz.',
            ])->withInput();
        }

        Order::create([
            'product_id' => $urun->id,
            'customer_name' => $veriler['customer_name'],
            'quantity' => $adet,
            'total_price' => $adet * $urun->price,
            'status' => 'Bekliyor',
        ]);

        $urun->decrement('stock', $adet);

        return redirect()->route('urunler.index')->with('success', 'Sipariş başarıyla oluşturuldu ve ürün stoğu güncellendi.');
    }
}
