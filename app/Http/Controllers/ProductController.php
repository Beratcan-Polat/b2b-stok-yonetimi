<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Product::class);

        $arama = $request->input('search');
        $kategoriId = $request->input('category_id');

        $urunSorgusu = Product::with('category');

        if ($arama) {
            $urunSorgusu->where('name', 'like', '%' . $arama . '%');
        }

        if ($kategoriId) {
            $urunSorgusu->where('category_id', $kategoriId);
        }

        $urunler = $urunSorgusu->latest()->paginate(10)->withQueryString();

        $kategoriler = Category::orderBy('name')->get();

        return view('urunler.index', compact('urunler', 'kategoriler'));
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

        return redirect()->route('urunler.index')->with('success', 'Ürün başarıyla eklendi.');
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

    public function silinenler()
    {
        Gate::authorize('viewTrashed', Product::class);

        $urunler = Product::onlyTrashed()->with('category')->latest('deleted_at')->paginate(10);

        return view('urunler.silinenler', compact('urunler'));
    }

    public function geriYukle($id)
    {
        Gate::authorize('restore', Product::class);

        $urun = Product::onlyTrashed()->findOrFail($id);

        $urun->restore();

        return redirect()->route('urunler.silinenler')->with('success', 'Ürün başarıyla geri yüklendi.');
    }
}
