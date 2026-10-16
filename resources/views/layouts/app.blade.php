<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'B2B Sipariş ve Stok Yönetimi')</title>

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @auth
        <script src="{{ asset('js/panel-menu.js') }}" defer></script>
    @endauth
</head>

<body>

    <a class="icerige-gec" href="#ana-icerik">İçeriğe geç</a>

    <div class="panel-duzeni @guest misafir-duzeni @endguest">
        @auth
            <aside class="yan-menu" aria-label="Panel menüsü">
                <div class="yan-menu-baslik">
                    <a href="{{ route('anasayfa') }}" class="logo yan-menu-logo">
                        B2B Stok Yönetimi
                    </a>

                    <button type="button" class="menu-dugmesi" aria-controls="panel-menu" aria-expanded="false">
                        Menü
                        <span aria-hidden="true">☰</span>
                    </button>
                </div>

                <nav id="panel-menu" class="menu" aria-label="Ana menü">
                    <a href="{{ route('anasayfa') }}" @class(['aktif' => request()->routeIs('anasayfa')])>Ana Sayfa</a>

                    @canany(['viewAny', 'create'], App\Models\Category::class)
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Category::class) ? route('kategoriler.index') : route('kategoriler.create') }}" @class(['aktif' => request()->routeIs('kategoriler.*')])>Kategoriler</a>
                    @endcanany

                    @canany(['viewAny', 'create'], App\Models\Product::class)
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Product::class) ? route('urunler.index') : route('urunler.create') }}" @class(['aktif' => request()->routeIs('urunler.*')])>Ürünler</a>
                    @endcanany

                    @canany(['viewAny', 'create'], App\Models\Order::class)
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Order::class) ? route('siparisler.index') : route('siparisler.urun-sec') }}" @class(['aktif' => request()->routeIs('siparisler.*')])>Siparişler</a>
                    @endcanany

                    @canany(['viewAny', 'create'], App\Models\User::class)
                        <a href="{{ auth()->user()->can('viewAny', App\Models\User::class) ? route('kullanicilar.index') : route('kullanicilar.create') }}" @class(['aktif' => request()->routeIs('kullanicilar.*')])>Kullanıcılar</a>
                    @endcanany

                    @can('viewAny', App\Models\Role::class)
                        <a href="{{ route('roller.index') }}" @class(['aktif' => request()->routeIs('roller.*')])>Roller ve Yetkiler</a>
                    @endcan
                </nav>
            </aside>
        @endauth

        <div class="panel-ana-alan">
            <header class="ust-alan">
                <div class="ust-menu">
                    @auth
                        <a href="{{ route('profil.goster') }}" class="kullanici-bilgi">
                            {{ auth()->user()->name }}
                        </a>

                        @if (session()->has('impersonator_id'))
                            <form action="{{ route('admin.impersonate.return') }}" method="POST" class="satir-ici-form">
                                @csrf

                                <button type="submit" class="buton buton-ikincil buton-kucuk">
                                    Süper Admin Hesabına Dön
                                </button>
                            </form>
                        @else
                            <form action="{{ route('cikis') }}" method="POST" class="satir-ici-form">
                                @csrf

                                <button type="submit" class="buton buton-cikis buton-kucuk">
                                    Çıkış Yap
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('giris') }}" class="logo">B2B Stok Yönetimi</a>
                    @endauth
                </div>
            </header>

            <main id="ana-icerik" class="kapsayici ana-icerik">
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
        </div>
    </div>
@stack('scripts')
</body>

</html>
