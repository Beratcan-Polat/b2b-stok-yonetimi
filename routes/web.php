<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

Route::view('/', 'anasayfa')->name('anasayfa');

Route::resource('kategoriler', CategoryController::class)->parameters([
        'kategoriler' => 'kategori',
    ])->except(['show']);

Route::resource('urunler', ProductController::class)->parameters([
        'urunler' => 'urun',
    ])->except(['show']);

Route::get('/siparisler', [OrderController::class, 'index'])->name('siparisler.index');

Route::get('/urunler/{urun}/siparis', [OrderController::class, 'create'])->name('siparisler.create');

Route::post('/urunler/{urun}/siparis', [OrderController::class, 'store'])->name('siparisler.store');

