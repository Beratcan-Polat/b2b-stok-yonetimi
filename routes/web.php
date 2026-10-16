<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/giris', [AuthController::class, 'girisFormu'])->name('giris');
    Route::post('/giris', [AuthController::class, 'giris'])->name('giris.gonder');
});

Route::middleware('auth')->group(function () {

    Route::post('/cikis', [AuthController::class, 'cikis'])->name('cikis');

    Route::view('/', 'anasayfa')->name('anasayfa');

    Route::post('/admin/impersonate/return', [ImpersonateController::class, 'geriDon'])->name('admin.impersonate.return');

    Route::post('/admin/impersonate/{kullanici}', [ImpersonateController::class, 'baslat'])
        ->middleware('can:admin')
        ->name('admin.impersonate.start');

    Route::get('/profil', [ProfilController::class, 'goster'])->name('profil.goster');

    Route::patch('/profil/sifre', [ProfilController::class, 'sifreGuncelle'])->middleware('throttle:6,1')->name('profil.sifre-guncelle');

    Route::get('/kullanicilar/veri', [UserController::class, 'veri'])->name('kullanicilar.veri');

    Route::resource('kullanicilar', UserController::class)->parameters([
        'kullanicilar' => 'kullanici',
    ])->except(['show']);

    Route::get('/roller/veri', [RoleController::class, 'veri'])->name('roller.veri');

    Route::resource('roller', RoleController::class)
        ->parameters([
            'roller' => 'rol',
        ])
        ->except(['show']);

    Route::get('/kategoriler/veri', [CategoryController::class, 'veri'])->name('kategoriler.veri');

    Route::resource('kategoriler', CategoryController::class)->parameters([
        'kategoriler' => 'kategori',
    ])->except(['show']);

    Route::get('/urunler/veri', [ProductController::class, 'veri'])->name('urunler.veri');

    Route::get('/urunler/silinenler/veri', [ProductController::class, 'silinenlerVeri'])
        ->name('urunler.silinenler.veri');

    Route::get('/urunler/silinenler', [ProductController::class, 'silinenler'])
        ->name('urunler.silinenler');

    Route::patch('/urunler/{id}/geri-yukle', [ProductController::class, 'geriYukle'])
        ->name('urunler.geri-yukle');

    Route::resource('urunler', ProductController::class)->parameters([
        'urunler' => 'urun',
    ])->except(['show']);

    Route::get('/siparisler', [OrderController::class, 'index'])->name('siparisler.index');

    Route::get('/siparisler/veri', [OrderController::class, 'veri'])->name('siparisler.veri');

    Route::get('/siparisler/urun-sec', [OrderController::class, 'selectProduct'])->name('siparisler.urun-sec');

    Route::get('/siparisler/urun-sec/veri', [OrderController::class, 'selectProductVeri'])
        ->name('siparisler.urun-sec.veri');

    Route::get('/urunler/{urun}/siparis', [OrderController::class, 'create'])
        ->name('siparisler.create');

    Route::post('/urunler/{urun}/siparis', [OrderController::class, 'store'])
        ->name('siparisler.store');
});
