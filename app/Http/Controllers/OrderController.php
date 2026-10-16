<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Order::class);

        return view('siparisler.index');
    }

    public function veri(): JsonResponse
    {
        Gate::authorize('viewAny', Order::class);

        $siparisler = Order::with('product')->latest()->get();

        $veriler = $siparisler->map(function (Order $siparis): array {
            return [
                'id' => $siparis->id,
                'product_name' => $siparis->product?->name ?? 'Silinmiş ürün',
                'product_sku' => $siparis->product?->sku ?? 'Ürün kaydı artık mevcut değil.',
                'customer_name' => $siparis->customer_name,
                'quantity' => $siparis->quantity,
                'total_price' => (float) $siparis->total_price,
                'status' => $siparis->status,
                'created_at' => $siparis->created_at->toIso8601String(),
                'created_at_display' => $siparis->created_at->format('d.m.Y H:i'),
            ];
        });

        return response()->json(['data' => $veriler]);
    }

    public function selectProduct(): View
    {
        Gate::authorize('create', Order::class);

        return view('siparisler.urun-sec');
    }

    public function selectProductVeri(): JsonResponse
    {
        Gate::authorize('create', Order::class);

        $urunler = Product::where('stock', '>', 0)->orderBy('name')->get();

        $veriler = $urunler->map(function (Product $urun): array {
            return [
                'name' => $urun->name,
                'sku' => $urun->sku,
                'stock' => $urun->stock,
                'actions' => view('siparisler._select_action', ['urun' => $urun])->render(),
            ];
        });

        return response()->json(['data' => $veriler]);
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

        $donus = Gate::allows('viewAny', Product::class) ? 'urunler.index' : 'siparisler.urun-sec';

        return redirect()->route($donus)->with('success', 'Sipariş başarıyla oluşturuldu ve ürün stoğu güncellendi.');
    }
}
