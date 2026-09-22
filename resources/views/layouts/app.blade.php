<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'B2B Sipariş ve Stok Yönetimi')</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

    <header class="ust-alan">
        <div class="kapsayici ust-menu">
            <a href="{{ route('anasayfa') }}" class="logo">
                B2B Stok Yönetimi
            </a>

            <nav class="menu">
                @auth
                    <a href="{{ route('anasayfa') }}">Ana Sayfa</a>

                    @can('viewAny', App\Models\Category::class)
                        <a href="{{ route('kategoriler.index') }}">Kategoriler</a>
                    @endcan

                    @can('viewAny', App\Models\Product::class)
                        <a href="{{ route('urunler.index') }}">Ürünler</a>
                    @endcan

                    @can('viewAny', App\Models\Order::class)
                        <a href="{{ route('siparisler.index') }}">Siparişler</a>
                    @endcan

                    @can('viewAny', App\Models\User::class)
                        <a href="{{ route('kullanicilar.index') }}">Kullanıcılar</a>
                    @endcan

                    <span class="kullanici-bilgi">
                        {{ auth()->user()->name }}
                    </span>

                    <form action="{{ route('cikis') }}" method="POST" class="satir-ici-form">
                        @csrf

                        <button type="submit" class="buton buton-ikincil buton-kucuk">
                            Çıkış
                        </button>
                    </form>
                @endauth
            </nav>
        </div>
    </header>

    <main class="kapsayici ana-icerik">

        @if (session('success'))
            <div class="uyari uyari-basarili">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="uyari uyari-hata">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="uyari uyari-hata">
                <strong>Lütfen aşağıdaki hataları düzeltin:</strong>

                <ul>
                    @foreach ($errors->all() as $hata)
                        <li>{{ $hata }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')

    </main>

    <footer class="alt-alan">
        <div class="kapsayici">
            B2B Sipariş ve Stok Yönetimi
        </div>
    </footer>

</body>
</html>
