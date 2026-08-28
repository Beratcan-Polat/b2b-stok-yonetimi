<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::view('/', 'anasayfa')->name('anasayfa');

Route::resource('kategoriler', CategoryController::class)->parameters([
        'kategoriler' => 'kategori',
    ])->except(['show']);
