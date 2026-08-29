@extends('layouts.app')

@section('title', 'Ürün Düzenle')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Ürün Düzenle</h1>
            <p>Ürün bilgilerini, stok miktarını veya görselini güncelleyin.</p>
        </div>
    </section>

    <form
        action="{{ route('urunler.update', $urun) }}"
        method="POST"
        enctype="multipart/form-data"
        class="form-kutusu form-dar"
    >
        @csrf
        @method('PUT')

        <div class="form-grup">
            <label for="category_id">Kategori</label>

            <select id="category_id" name="category_id" required>
                <option value="">Kategori seçin</option>

                @foreach ($kategoriler as $kategori)
                    <option
                        value="{{ $kategori->id }}"
                        @selected(old('category_id', $urun->category_id) == $kategori->id)
                    >
                        {{ $kategori->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-grup">
            <label for="name">Ürün Adı</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $urun->name) }}"
                maxlength="255"
                required
            >
        </div>

        <div class="form-grup">
            <label for="sku">SKU</label>

            <input
                type="text"
                id="sku"
                name="sku"
                value="{{ old('sku', $urun->sku) }}"
                maxlength="100"
                required
            >
        </div>

        <div class="form-grup">
            <label for="price">Fiyat</label>

            <input
                type="number"
                id="price"
                name="price"
                value="{{ old('price', $urun->price) }}"
                min="0"
                step="0.01"
                required
            >
        </div>

        <div class="form-grup">
            <label for="stock">Stok Adedi</label>

            <input
                type="number"
                id="stock"
                name="stock"
                value="{{ old('stock', $urun->stock) }}"
                min="0"
                step="1"
                required
            >
        </div>

        @if ($urun->image_path)
            <div class="form-grup">
                <label>Mevcut Görsel</label>

                <img
                    src="{{ asset('storage/' . $urun->image_path) }}"
                    alt="{{ $urun->name }}"
                    class="mevcut-gorsel"
                >
            </div>
        @endif

        <div class="form-grup">
            <label for="image">Yeni Görsel</label>

            <input
                type="file"
                id="image"
                name="image"
                accept=".jpeg,.jpg,.png,.webp"
            >

            <small class="form-aciklama">
                Yeni görsel seçmezseniz mevcut görsel korunur.
            </small>
        </div>

        <div class="form-butonlari">
            <button type="submit" class="buton buton-birincil">
                Değişiklikleri Kaydet
            </button>

            <a href="{{ route('urunler.index') }}" class="buton buton-ikincil">
                İptal
            </a>
        </div>
    </form>

@endsection