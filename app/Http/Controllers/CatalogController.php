<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CatalogController extends Controller
{
    public function index(CatalogRequest $request): View
    {
        $filtreler = $request->validated();
        $arama = $filtreler['arama'] ?? '';
        $kategoriId = $filtreler['kategori'] ?? null;
        $siralama = $filtreler['siralama'] ?? 'varsayilan';

        [$sutun, $yon] = match ($siralama) {
            'fiyat-artan' => ['price', 'asc'],
            'fiyat-azalan' => ['price', 'desc'],
            'tarih-eski' => ['created_at', 'asc'],
            'ad-az' => ['name', 'asc'],
            'ad-za' => ['name', 'desc'],
            default => ['created_at', 'desc'],
        };

        $kategoriler = Category::query()
            ->orderBy(app()->getLocale() === 'en' ? 'name_en' : 'name')
            ->orderBy('id')
            ->get(['id', 'name', 'name_en']);

        $urunSorgusu = Product::query()->with('category:id,name,name_en');

        if ($arama !== '') {
            $urunSorgusu->where(function (Builder $sorgu) use ($arama): void {
                $sorgu->where('name', 'like', '%'.$arama.'%')
                    ->orWhere('sku', 'like', '%'.$arama.'%');
            });

        }

        if ($kategoriId !== null) {
            $urunSorgusu->where('category_id', $kategoriId);
        }

        $urunler = $urunSorgusu
            ->orderBy($sutun, $yon)
            ->orderBy('id', $yon)
            ->paginate(12)
            ->appends($request->safe()->only(['arama', 'kategori', 'siralama']));

        $template = SiteSetting::catalogTheme();

        return view($template.'.index', compact(
            'urunler',
            'kategoriler',
            'arama',
            'kategoriId',
            'siralama',
        ));

    }

    public function gorsel(Product $urun)
    {
        if (blank($urun -> image_path)){
            abort(404);
        }

        $anahtar = 'urun-gorseli:'.$urun->id;

        $gorsel = Cache::store('redis')->get($anahtar);

        if ($gorsel !== null) {
            $durum = "Redis'ten";
        }
        else {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');

            if (! $disk->exists($urun->image_path)) {
                abort(404);
            }

            $icerik = $disk->get($urun->image_path);

            if (! is_string($icerik)) {
                abort(404);
            }

            $gorsel = [
                'icerik' => $icerik,
                'tur' => $disk->mimeType($urun->image_path) ?: 'application/octet-stream',
            ];

            Cache::store('redis')->put($anahtar, $gorsel, 60);

            $durum = "Dosya'dan";
        }

        return response($gorsel['icerik'])
            ->header('Content-Type', $gorsel['tur'])
            ->header('X-Image-Cache', $durum);
    }
}
