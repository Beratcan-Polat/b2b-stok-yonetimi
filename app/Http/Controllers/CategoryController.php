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
                'name' => $kategori->localizedName(),
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
                'name' => __('panel.category-slug-used'),
            ])->withInput();
        }

        Category::create([
            'name' => $veriler['name'],
            'name_en' => $veriler['name_en'] ?? null,
            'slug' => $slug,
        ]);

        $donus = Gate::allows('viewAny', Category::class) ? 'kategoriler.index' : 'anasayfa';

        return redirect()->route($donus)->with('success', __('panel.category-created'));
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
                'name' => __('panel.category-slug-used'),
            ])->withInput();
        }

        $kategori->update([
            'name' => $veriler['name'],
            'name_en' => array_key_exists('name_en', $veriler) ? $veriler['name_en'] : $kategori->name_en,
            'slug' => $slug,
        ]);

        $donus = Gate::allows('viewAny', Category::class) ? 'kategoriler.index' : 'anasayfa';

        return redirect()->route($donus)->with('success', __('panel.category-updated'));
    }

    public function destroy(Category $kategori)
    {
        Gate::authorize('delete', $kategori);

        $kategori->delete();

        $donus = Gate::allows('viewAny', Category::class) ? 'kategoriler.index' : 'anasayfa';

        return redirect()->route($donus)->with('success', __('panel.category-deleted'));
    }
}
