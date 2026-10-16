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

        @canany(['viewAny', 'create'], App\Models\Category::class)
            <article class="kart">
                <div class="kart-ikon">K</div>

                <h2>Kategori Yönetimi</h2>

                <p>
                    Ürünlerin bağlı olacağı kategorileri ekleyin, düzenleyin ve yönetin.
                </p>

                <a href="{{ auth()->user()->can('viewAny', App\Models\Category::class) ? route('kategoriler.index') : route('kategoriler.create') }}" class="buton buton-birincil">
                    {{ auth()->user()->can('viewAny', App\Models\Category::class) ? 'Kategorileri Yönet' : 'Kategori Ekle' }}
                </a>
            </article>
        @endcanany

        @canany(['viewAny', 'create'], App\Models\Product::class)
            <article class="kart">
                <div class="kart-ikon">Ü</div>

                <h2>Ürün Yönetimi</h2>

                <p>
                    Ürün bilgilerini, fiyatlarını, stoklarını ve görsellerini yönetin.
                </p>

                <a href="{{ auth()->user()->can('viewAny', App\Models\Product::class) ? route('urunler.index') : route('urunler.create') }}" class="buton buton-birincil">
                    {{ auth()->user()->can('viewAny', App\Models\Product::class) ? 'Ürünleri Yönet' : 'Ürün Ekle' }}
                </a>
            </article>
        @endcanany

        @canany(['viewAny', 'create'], App\Models\Order::class)
            <article class="kart">
                <div class="kart-ikon">S</div>

                <h2>Sipariş Yönetimi</h2>

                <p>
                    Müşteri siparişlerini oluşturun ve ürün stoklarını takip edin.
                </p>

                <a href="{{ auth()->user()->can('viewAny', App\Models\Order::class) ? route('siparisler.index') : route('siparisler.urun-sec') }}" class="buton buton-birincil">
                    {{ auth()->user()->can('viewAny', App\Models\Order::class) ? 'Siparişleri Görüntüle' : 'Sipariş Oluştur' }}
                </a>
            </article>
        @endcanany

        @canany(['viewAny', 'create'], App\Models\User::class)
            <article class="kart">
                <div class="kart-ikon">P</div>

                <h2>Kullanıcı Yönetimi</h2>

                <p>
                    Panel kullanıcılarını ekleyin ve rollerini belirleyin.
                </p>

                <a href="{{ auth()->user()->can('viewAny', App\Models\User::class) ? route('kullanicilar.index') : route('kullanicilar.create') }}" class="buton buton-birincil">
                    {{ auth()->user()->can('viewAny', App\Models\User::class) ? 'Kullanıcıları Yönet' : 'Kullanıcı Ekle' }}
                </a>
            </article>
        @endcanany

        @can('viewAny', App\Models\Role::class)
            <article class="kart">
                <div class="kart-ikon">R</div>

                <h2>Roller ve Yetkiler</h2>

                <p>
                    Yeni roller oluşturun ve kullanıcıların erişebileceği işlemleri belirleyin.
                </p>

                <a href="{{ route('roller.index') }}" class="buton buton-birincil">
                    Rolleri Yönet
                </a>
            </article>
        @endcan

    </section>

@endsection
