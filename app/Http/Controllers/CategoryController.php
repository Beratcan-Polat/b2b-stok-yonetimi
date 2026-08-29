<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function index()
    {
        $kategoriler = Category::withCount('products')->latest()->get();

        return view('kategoriler.index', compact('kategoriler'));
    }


    public function create()
    {
        return view('kategoriler.create');
    }


    public function store(CategoryRequest $request)
    {


        $veriler = $request->validated();


        $slug = Str::slug($veriler['name']);

        $slugKullaniliyor = Category::where('slug', $slug)->exists();

        if ($slugKullaniliyor) {
            return back()->withErrors([
                'name' => 'Bu kategori adına ait adres bilgisi daha önce kullanılmış.',
            ])->withInput();
        }

        Category::create([
            'name' => $veriler['name'],
            'slug' => $slug
        ]);

        return redirect()->route('kategoriler.index')->with('succes', 'Kategori başarıyla eklendi.');
    }


    public function edit(Category $kategori)
    {
        return view('kategoriler.edit', compact('kategori'));
    }


    public function update(Request $request, Category $kategori)
    {
        $veriler = $request->validate(
            [
                'name' => 'required|string|max:255|unique:categories,name,' . $kategori->id,
            ],
            [
                'name.required' => 'Kategori adı zorunludur.',
                'name.string' => 'Kategori adı metin olmalıdır.',
                'name.max' => 'Kategori en fazla 255 karakter olabilir.',
                'name.unique' => 'Bu kategori adı daha önce kullanıldı.'
            ]
        );

        $slug = Str::slug($veriler['name']);

        $slugKullaniliyor = Category::where('slug', $slug)->where('id', '!=', $kategori->id)->exists();

        if ($slugKullaniliyor) {
            return back()->withErrors([
                'name' => 'Bu kategori adına ait adres bilgisi daha önce kullanılmış.'
            ])->withInput();
        }

        $kategori->update([
            'name' => $veriler['name'],
            'slug' => $slug,
        ]);

        return redirect()->route('kategoriler.index')->with('success', 'Kategori başarıyla güncellendi.');
    }


    public function destroy(Category $kategori)
    {
        if ($kategori->products()->exists()) {
            return redirect()->route('kategoriler.index')->with('error', 'Bu kategoriye bağlı ürünler bulunduğu için kategori silinemez.');
        }

        $kategori->delete();

        return redirect()->route('kategoriler.index')->with('success', 'Kategori başarıyla silindi.');
    }
}
