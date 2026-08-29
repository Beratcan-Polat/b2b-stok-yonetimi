@extends('layouts.app')

@section('title', 'Yeni Ürün Ekle')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Yeni Ürün Ekle</h1>
            <p>Ürün bilgilerini ve isteğe bağlı ürün görselini girin.</p>
        </div>
    </section>

    <form
        action="{{ route('urunler.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="form-kutusu form-dar"
    >
        @csrf

        <div class="form-grup">
            <label for="category_id">Kategori</label>

            <select id="category_id" name="category_id" required>
                <option value="">Kategori seçin</option>

                @foreach ($kategoriler as $kategori)
                    <option
                        value="{{ $kategori->id }}"
                        @selected(old('category_id') == $kategori->id)
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
                value="{{ old('name') }}"
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
                value="{{ old('sku') }}"
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
                value="{{ old('price') }}"
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
                value="{{ old('stock', 0) }}"
                min="0"
                step="1"
                required
            >
        </div>

        <div class="form-grup">
            <label for="image">Ürün Görseli</label>

            <input
                type="file"
                id="image"
                name="image"
                accept=".jpeg,.jpg,.png,.webp"
            >

            <small class="form-aciklama">
                JPEG, JPG, PNG veya WEBP — en fazla 2 MB.
            </small>
        </div>

        <div class="form-butonlari">
            <button type="submit" class="buton buton-birincil">
                Ürünü Kaydet
            </button>

            <a href="{{ route('urunler.index') }}" class="buton buton-ikincil">
                İptal
            </a>
        </div>
    </form>

@endsection