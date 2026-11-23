<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('katalog.description') }}">
    <title>@yield('title', __('katalog.title')) | {{ __('panel.title') }}</title>
    <link rel="stylesheet" href="{{ asset('adminkit/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/katalog3.css') }}">
    <link rel="stylesheet" href="{{ asset('css/katalog-sepet.css') }}">
    @stack('styles')
</head>
<body class="magaza">

    <header class="magaza-baslik">
        <div class="magaza-kapsayici magaza-ust-menu">
            <a href="{{ route('katalog.index') }}" class="magaza-marka">
                <span class="magaza-marka-isaret" aria-hidden="true">B2B</span>
                <span>
                    <span class="magaza-marka-adi">{{ __('panel.title') }}</span>
                    <span class="magaza-marka-aciklama">{{ __('katalog.title') }}</span>
                </span>
            </a>

            <form action="{{ route('katalog.index') }}" method="GET" class="magaza-arama" role="search" aria-label="{{ __('katalog.search-label') }}">
                <label for="arama" class="visually-hidden">{{ __('katalog.search-label') }}</label>
                <input type="search" id="arama" name="arama" value="{{ $arama }}"
                    placeholder="{{ __('katalog.search-placeholder') }}" maxlength="100">
                <input type="hidden" name="kategori" value="{{ $kategoriId }}">
                <input type="hidden" name="siralama" value="{{ $siralama }}">
                <button type="submit">{{ __('katalog.search-label') }}</button>
            </form>

            <div class="katalog-sepet-araclar">
                @include('katalog._sepet-baglantisi')
                <div class="magaza-dil">
                    @include('layouts._adminkit_language')
                </div>
            </div>
        </div>

        <nav class="magaza-kategori-menu" aria-label="{{ __('panel.categories') }}">
            <div class="magaza-kapsayici magaza-kategori-baglantilar">
                <a href="{{ route('katalog.index', ['arama' => $arama, 'siralama' => $siralama]) }}"
                    @class(['magaza-kategori-baglantisi', 'aktif' => $kategoriId === null])
                    @if ($kategoriId === null) aria-current="true" @endif>{{ __('panel.all-categories') }}</a>
                @foreach ($kategoriler as $kategori)
                    <a href="{{ route('katalog.index', ['arama' => $arama, 'kategori' => $kategori->id, 'siralama' => $siralama]) }}"
                        @class(['magaza-kategori-baglantisi', 'aktif' => (string) $kategoriId === (string) $kategori->id])
                        @if ((string) $kategoriId === (string) $kategori->id) aria-current="true" @endif>{{ $kategori->localizedName() }}</a>
                @endforeach
            </div>
        </nav>
    </header>

    <main id="ana-icerik" class="magaza-kapsayici magaza-icerik">
        @include('layouts._adminkit_alerts')
        @yield('content')
    </main>

    <footer class="magaza-alt-alan">
        <div class="magaza-kapsayici magaza-alt-icerik">
            <a href="{{ route('katalog.index') }}" class="magaza-alt-marka">{{ __('panel.title') }}</a>
            <span>{{ __('katalog.title') }}</span>
            <a href="{{ route('katalog.index') }}">{{ __('katalog.all-products') }} <span aria-hidden="true">→</span></a>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
