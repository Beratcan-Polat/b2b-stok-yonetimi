@extends('layouts.app')

@section('title', 'Hızlı Sipariş Ver')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Hızlı Sipariş Ver</h1>
            <p>Seçilen ürün için müşteri ve sipariş bilgilerini girin.</p>
        </div>
    </section>

    <div class="siparis-ozet">
        <h2>{{ $urun->name }}</h2>

        <div class="ozet-grid">
            <div>
                <span class="ozet-baslik">SKU</span>
                <strong>{{ $urun->sku }}</strong>
            </div>

            <div>
                <span class="ozet-baslik">Birim Fiyat</span>
                <strong>
                    {{ number_format($urun->price, 2, ',', '.') }} ₺
                </strong>
            </div>

            <div>
                <span class="ozet-baslik">Mevcut Stok</span>
                <strong>{{ $urun->stock }} adet</strong>
            </div>
        </div>
    </div>

    <form
        action="{{ route('siparisler.store', $urun) }}"
        method="POST"
        class="form-kutusu form-dar"
    >
        @csrf

        <div class="form-grup">
            <label for="customer_name">Müşteri Adı</label>

            <input
                type="text"
                id="customer_name"
                name="customer_name"
                value="{{ old('customer_name') }}"
                maxlength="255"
                required
                autofocus
            >
        </div>

        <div class="form-grup">
            <label for="quantity">Sipariş Adedi</label>

            <input
                type="number"
                id="quantity"
                name="quantity"
                value="{{ old('quantity', 1) }}"
                min="1"
                max="{{ $urun->stock }}"
                step="1"
                required
            >

            <small class="form-aciklama">
                En fazla {{ $urun->stock }} adet sipariş verebilirsiniz.
                Toplam tutar sistem tarafından otomatik hesaplanacaktır.
            </small>
        </div>

        <div class="form-butonlari">
            <button type="submit" class="buton buton-birincil">
                Siparişi Oluştur
            </button>

            <a href="{{ route('urunler.index') }}" class="buton buton-ikincil">
                İptal
            </a>
        </div>
    </form>

@endsection
