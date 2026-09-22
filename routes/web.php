<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

Route::middleware('guest')->group(function () {
    Route::get('/giris', [AuthController::class, 'girisFormu'])->name('giris');
    Route::post('/giris', [AuthController::class, 'giris'])->name('giris.gonder');
});

Route::middleware('auth')->group(function () {

    Route::post('/cikis', [AuthController::class, 'cikis'])->name('cikis');

    Route::view('/', 'anasayfa')->name('anasayfa');

    Route::resource('kullanicilar', UserController::class)->parameters([
        'kullanicilar' => 'kullanici',
    ])->except(['show']);

    Route::resource('kategoriler', CategoryController::class)->parameters([
        'kategoriler' => 'kategori',
    ])->except(['show']);

    Route::get('/urunler/silinenler', [ProductController::class, 'silinenler'])
        ->name('urunler.silinenler');

    Route::patch('/urunler/{id}/geri-yukle', [ProductController::class, 'geriYukle'])
        ->name('urunler.geri-yukle');

    Route::resource('urunler', ProductController::class)->parameters([
        'urunler' => 'urun',
    ])->except(['show']);

    Route::get('/siparisler', [OrderController::class, 'index'])->name('siparisler.index');

    Route::get('/urunler/{urun}/siparis', [OrderController::class, 'create'])
        ->name('siparisler.create');

    Route::post('/urunler/{urun}/siparis', [OrderController::class, 'store'])
        ->name('siparisler.store');
});
