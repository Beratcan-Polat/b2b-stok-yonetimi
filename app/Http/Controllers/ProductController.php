<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Product::class);

        $kategoriler = Category::orderBy('name')->get();

        return view('urunler.index', compact('kategoriler'));
    }

    public function veri(): JsonResponse
    {
        Gate::authorize('viewAny', Product::class);

        $urunler = Product::with('category')->latest()->get();

        $veriler = $urunler->map(function (Product $urun): array {
            return [
                'image' => view('urunler._image', ['urun' => $urun])->render(),
                'name' => $urun->name,
                'sku' => $urun->sku,
                'category_name' => $urun->category?->name ?? 'Silinmiş kategori',
                'price' => (float) $urun->price,
                'stock' => $urun->stock,
                'actions' => view('urunler._actions', ['urun' => $urun])->render(),
            ];
        });

        return response()->json(['data' => $veriler]);
    }

    public function create()
    {
        Gate::authorize('create', Product::class);

        $kategoriler = Category::orderBy('name')->get();

        return view('urunler.create', compact('kategoriler'));
    }

    public function store(ProductRequest $request)
    {
        $veriler = $request->validated();

        $gorselYolu = null;

        if ($request->hasFile('image')) {
            $gorselYolu = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'category_id' => $veriler['category_id'],
            'name' => $veriler['name'],
            'sku' => $veriler['sku'],
            'price' => $veriler['price'],
            'stock' => $veriler['stock'],
            'image_path' => $gorselYolu,
        ]);

        $donus = Gate::allows('viewAny', Product::class) ? 'urunler.index' : 'anasayfa';

        return redirect()->route($donus)->with('success', 'Ürün başarıyla eklendi.');
    }

    public function edit(Product $urun)
    {
        Gate::authorize('update', $urun);

        $kategoriler = Category::orderBy('name')->get();

        return view('urunler.edit', compact('urun', 'kategoriler'));
    }

    public function update(ProductRequest $request, Product $urun)
    {
        $veriler = $request->validated();

        $gorselYolu = $urun->image_path;

        if ($request->hasFile('image')) {
            if ($urun->image_path) {
                Storage::disk('public')->delete($urun->image_path);
            }

            $gorselYolu = $request->file('image')->store('products', 'public');
        }

        $urun->update([
            'category_id' => $veriler['category_id'],
            'name' => $veriler['name'],
            'sku' => $veriler['sku'],
            'price' => $veriler['price'],
            'stock' => $veriler['stock'],
            'image_path' => $gorselYolu,
        ]);

        return redirect()->route('urunler.index')->with('success', 'Ürün başarıyla güncellendi.');
    }

    public function destroy(Product $urun)
    {
        Gate::authorize('delete', $urun);

        $urun->delete();

        return redirect()->route('urunler.index')->with('success', 'Ürün başarıyla silindi.');
    }

    public function silinenler(): View
    {
        Gate::authorize('viewTrashed', Product::class);

        return view('urunler.silinenler');
    }

    public function silinenlerVeri(): JsonResponse
    {
        Gate::authorize('viewTrashed', Product::class);

        $urunler = Product::onlyTrashed()->with('category')->latest('deleted_at')->get();

        $veriler = $urunler->map(function (Product $urun): array {
            return [
                'image' => view('urunler._image', ['urun' => $urun])->render(),
                'name' => $urun->name,
                'sku' => $urun->sku,
                'category_name' => $urun->category?->name ?? 'Silinmiş kategori',
                'stock' => $urun->stock,
                'deleted_at' => $urun->deleted_at->toIso8601String(),
                'deleted_at_display' => $urun->deleted_at->format('d.m.Y H:i'),
                'actions' => view('urunler._restore_action', ['urun' => $urun])->render(),
            ];
        });

        return response()->json(['data' => $veriler]);
    }

    public function geriYukle($id)
    {
        Gate::authorize('restore', Product::class);

        $urun = Product::onlyTrashed()->findOrFail($id);

        $urun->restore();

        return redirect()->route('urunler.silinenler')->with('success', 'Ürün başarıyla geri yüklendi.');
    }
}
