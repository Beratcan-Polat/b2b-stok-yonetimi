<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', __('panel.full-title'))</title>

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @auth
        <script src="{{ asset('js/panel-menu.js') }}" defer></script>
    @endauth
</head>

<body>

    <a class="icerige-gec" href="#ana-icerik">{{ __('panel.skip-content') }}</a>

    <div class="panel-duzeni @guest misafir-duzeni @endguest">
        @auth
            <aside class="yan-menu" aria-label="{{ __('panel.panel-menu') }}">
                <div class="yan-menu-baslik">
                    <a href="{{ route('anasayfa') }}" class="logo yan-menu-logo">
                        {{ __('panel.home') }} {{ __('panel.title') }}
                    </a>

                    <button type="button" class="menu-dugmesi" aria-controls="panel-menu" aria-expanded="false">
                        {{ __('panel.menu') }}
                        <span aria-hidden="true">☰</span>
                    </button>
                </div>

                <nav id="panel-menu" class="menu" aria-label="{{ __('panel.main-menu') }}">
                    <a href="{{ route('anasayfa') }}" @class(['aktif' => request()->routeIs('anasayfa')])> {{ __('panel.home') }} </a>

                    @canany(['viewAny', 'create'], App\Models\Category::class)
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Category::class) ? route('kategoriler.index') : route('kategoriler.create') }}"
                            @class(['aktif' => request()->routeIs('kategoriler.*')])> {{ __('panel.categories') }} </a>
                    @endcanany

                    @canany(['viewAny', 'create'], App\Models\Product::class)
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Product::class) ? route('urunler.index') : route('urunler.create') }}"
                            @class(['aktif' => request()->routeIs('urunler.*')])> {{ __('panel.products') }} </a>
                    @endcanany

                    @canany(['viewAny', 'create'], App\Models\Order::class)
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Order::class) ? route('siparisler.index') : route('siparisler.urun-sec') }}"
                            @class(['aktif' => request()->routeIs('siparisler.*')])> {{ __('panel.orders') }} </a>
                    @endcanany

                    @canany(['viewAny', 'create'], App\Models\User::class)
                        <a href="{{ auth()->user()->can('viewAny', App\Models\User::class) ? route('kullanicilar.index') : route('kullanicilar.create') }}"
                            @class(['aktif' => request()->routeIs('kullanicilar.*')])> {{ __('panel.users') }} </a>
                    @endcanany

                    @can('viewAny', App\Models\Role::class)
                        <a href="{{ route('roller.index') }}" @class(['aktif' => request()->routeIs('roller.*')])> {{ __('panel.roles') }} </a>
                    @endcan
                </nav>
            </aside>
        @endauth

        <div class="panel-ana-alan">
            <header class="ust-alan">
                <div class="ust-menu">
                    @guest
                        <a href="{{ route('giris') }}" class="logo">{{ __('panel.title') }}</a>
                    @endguest

                    <form action="{{ route('dil.degistir') }}" method="POST" class="dil-formu">
                        @csrf

                        <div class="dil-secici">
                            <svg class="dil-simgesi" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true" focusable="false">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M3 12h18M12 3a17 17 0 0 1 0 18 17 17 0 0 1 0-18Z" />
                            </svg>

                            <select name="locale" class="ust-kontrol" aria-label="{{ __('panel.language') }}" onchange="this.form.submit()">
                                <option value="tr" lang="tr" @selected(app()->getLocale() === 'tr')>Türkçe</option>
                                <option value="en" lang="en" @selected(app()->getLocale() === 'en')>English</option>
                            </select>
                        </div>
                    </form>
                    @auth
                        <a href="{{ route('profil.goster') }}" class="kullanici-bilgi ust-kontrol" title="{{ auth()->user()->name }}">
                            <span class="ust-kontrol-metni">{{ auth()->user()->name }}</span>
                        </a>

                        @if (session()->has('impersonator_id'))
                            <form action="{{ route('admin.impersonate.return') }}" method="POST" class="satir-ici-form">
                                @csrf

                                <button type="submit" class="buton buton-ikincil ust-kontrol" title="{{ __('panel.return_to_admin') }}">
                                    <span class="ust-kontrol-metni">{{ __('panel.return_to_admin') }}</span>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('cikis') }}" method="POST" class="satir-ici-form">
                                @csrf

                                <button type="submit" class="buton buton-cikis ust-kontrol">
                                    <span class="ust-kontrol-metni">{{ __('panel.logout') }}</span>
                                </button>
                            </form>
                        @endif
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
                        <strong>{{ __('panel.fix-errors') }}</strong>

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
                    {{ __('panel.title') }}
                </div>
            </footer>
        </div>
    </div>
    @stack('scripts')
</body>

</html>
