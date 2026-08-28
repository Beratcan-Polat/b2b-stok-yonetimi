@extends('layouts.app')

@section('title', 'Ana Sayfa')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Yönetim Paneli</h1>
            <p>
                Kategori, ürün, sipariş ve stok işlemlerini bu panel üzerinden yönetebilirsiniz.
            </p>
        </div>
    </section>

    <section class="kart-grid">

        <article class="kart">
            <div class="kart-ikon">K</div>

            <h2>Kategori Yönetimi</h2>

            <p>
                Ürünlerin bağlı olacağı kategorileri ekleyin, düzenleyin ve yönetin.
            </p>

            <span class="durum-etiketi">Sonraki aşama</span>
        </article>

        <article class="kart">
            <div class="kart-ikon">Ü</div>

            <h2>Ürün Yönetimi</h2>

            <p>
                Ürün bilgilerini, fiyatlarını, stoklarını ve görsellerini yönetin.
            </p>

            <span class="durum-etiketi">Hazırlanacak</span>
        </article>

        <article class="kart">
            <div class="kart-ikon">S</div>

            <h2>Sipariş Yönetimi</h2>

            <p>
                Müşteri siparişlerini oluşturun ve ürün stoklarını takip edin.
            </p>

            <span class="durum-etiketi">Hazırlanacak</span>
        </article>

    </section>

@endsection