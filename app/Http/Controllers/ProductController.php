<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $arama = $request->input('search');
        $kategoriId = $request->input('category_id');

        $urunSorgusu = Product::with('category');

        if($arama)
            $urunSorgusu->where('name', 'like', '%'. $arama. '%');

        if($kategoriId)
            $urunSorgusu->where('category_id', $kategoriId);

        $urunler = $urunSorgusu->latest()->paginate(10)->withQueryString();

        $kategoriler = Category::orderBy('name')->get();

        return view('urunler.index', compact('urunler', 'kategoriler'));
    }


    public function create()
    {
        $kategoriler = Category::orderBy('name')->get();

        return view('urunler.create', compact('kategoriler'));
    }


    public function store(Request $request)
    {
        $veriler = $request->validate(
            [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:100|unique:products,sku',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
            ],
            [
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
            ]
        );

        $gorselYolu = null;

        if($request->hasFile('image'))
            {
                $gorselYolu = $request->file('image')->store('product', 'public');
            }

        Product::create([
            'category_id' => $veriler['category_id'],
            'name' => $veriler['name'],
            'sku' => $veriler['sku'],
            'price' => $veriler['price'],
            'stock' => $veriler['stock'],
            'image_path' => $gorselYolu,
        ]);

        return redirect()->route('urunler.index')->with('success', 'Ürünler başarıyla eklendi.');
            
            
    }


    public function edit(Product $urun)
    {
        $kategoriler = Category::orderBy('name')->get();

        return view('urunler.edit', compact('urun', 'kategoriler'));
    }


    public function update(Request $request, Product $urun)
    {
        $veriler = $request->validate(
            [
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:100|unique:products,sku,' . $urun->id,
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ],
            [
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
            ]
        );

        $gorselYolu = $urun->image_path;

        if($request->hasFile('image'))
            {
                if ($urun->image_path)
                    Storage::disk('public')->delete($urun->image_path);

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
        $urun->delete();

        return redirect()->route('urunler.index')->with('success', 'Ürün başarıyla silindi.');
    }
}
