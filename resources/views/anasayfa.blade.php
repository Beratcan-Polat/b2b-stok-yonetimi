@extends('layouts.app')

@section('title', 'Ana Sayfa')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Yönetim Paneli</h1>
            <p>
                Hoş geldiniz {{ auth()->user()->name }}. Yetkiniz dahilindeki işlemleri bu panel üzerinden yönetebilirsiniz.
            </p>
        </div>
    </section>

    <section class="kart-grid">

        @can('viewAny', App\Models\Category::class)
            <article class="kart">
                <div class="kart-ikon">K</div>

                <h2>Kategori Yönetimi</h2>

                <p>
                    Ürünlerin bağlı olacağı kategorileri ekleyin, düzenleyin ve yönetin.
                </p>

                <a href="{{ route('kategoriler.index') }}" class="buton buton-birincil">
                    Kategorileri Yönet
                </a>
            </article>
        @endcan

        @can('viewAny', App\Models\Product::class)
            <article class="kart">
                <div class="kart-ikon">Ü</div>

                <h2>Ürün Yönetimi</h2>

                <p>
                    Ürün bilgilerini, fiyatlarını, stoklarını ve görsellerini yönetin.
                </p>

                <a href="{{ route('urunler.index') }}" class="buton buton-birincil">
                    Ürünleri Yönet
                </a>
            </article>
        @endcan

        @can('viewAny', App\Models\Order::class)
            <article class="kart">
                <div class="kart-ikon">S</div>

                <h2>Sipariş Yönetimi</h2>

                <p>
                    Müşteri siparişlerini oluşturun ve ürün stoklarını takip edin.
                </p>

                <a href="{{ route('siparisler.index') }}" class="buton buton-birincil">
                    Siparişleri Görüntüle
                </a>
            </article>
        @endcan

        @can('viewAny', App\Models\User::class)
            <article class="kart">
                <div class="kart-ikon">P</div>

                <h2>Kullanıcı Yönetimi</h2>

                <p>
                    Panel kullanıcılarını ekleyin ve rollerini belirleyin.
                </p>

                <a href="{{ route('kullanicilar.index') }}" class="buton buton-birincil">
                    Kullanıcıları Yönet
                </a>
            </article>
        @endcan

    </section>

@endsection
