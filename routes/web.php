<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::view('/', 'anasayfa')->name('anasayfa');

Route::resource('kategoriler', CategoryController::class)->parameters([
        'kategoriler' => 'kategori',
    ])->except(['show']);

Route::resource('urunler', ProductController::class)->parameters([
        'urunler' => 'urun',
    ])->except(['show']);
