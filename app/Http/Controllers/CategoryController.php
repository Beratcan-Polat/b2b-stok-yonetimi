<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Category::class);

        return view('kategoriler.index');
    }

    public function veri(): JsonResponse
    {
        Gate::authorize('viewAny', Category::class);

        $kategoriler = Category::withCount('products')->latest()->get();

        $veriler = $kategoriler->map(function (Category $kategori): array {
            return [
                'id' => $kategori->id,
                'name' => $kategori->name,
                'slug' => $kategori->slug,
                'products_count' => $kategori->products_count,
                'actions' => view('kategoriler._actions', [
                    'kategori' => $kategori,
                ])->render(),
            ];
        });

        return response()->json(['data' => $veriler]);

    }

    public function create()
    {
        Gate::authorize('create', Category::class);

        return view('kategoriler.create');
    }

    public function store(CategoryRequest $request)
    {
        $veriler = $request->validated();

        $slug = Str::slug($veriler['name']);

        if (Category::where('slug', $slug)->exists()) {
            return back()->withErrors([
                'name' => 'Bu kategori adına ait adres bilgisi daha önce kullanılmış.',
            ])->withInput();
        }

        Category::create([
            'name' => $veriler['name'],
            'slug' => $slug,
        ]);

        $donus = Gate::allows('viewAny', Category::class) ? 'kategoriler.index' : 'anasayfa';

        return redirect()->route($donus)->with('success', 'Kategori başarıyla eklendi.');
    }

    public function edit(Category $kategori)
    {
        Gate::authorize('update', $kategori);

        return view('kategoriler.edit', compact('kategori'));
    }

    public function update(CategoryRequest $request, Category $kategori)
    {
        $veriler = $request->validated();

        $slug = Str::slug($veriler['name']);

        $slugKullaniliyor = Category::where('slug', $slug)
            ->where('id', '!=', $kategori->id)
            ->exists();

        if ($slugKullaniliyor) {
            return back()->withErrors([
                'name' => 'Bu kategori adına ait adres bilgisi daha önce kullanılmış.',
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
        Gate::authorize('delete', $kategori);

        $kategori->delete();

        return redirect()->route('kategoriler.index')->with('success', 'Kategori ve bağlı ürünler başarıyla silindi.');
    }
}
