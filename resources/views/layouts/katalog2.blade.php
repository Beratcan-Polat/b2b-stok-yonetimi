<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('katalog.description') }}">
    <title>@yield('title', __('katalog.title')) | {{ __('panel.title') }}</title>
    <link rel="stylesheet" href="{{ asset('adminkit/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/katalog2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/katalog-sepet.css') }}">
    @stack('styles')
</head>
<body class="katalog katalog-tema-iki">

    <header class="katalog-baslik">
        <div class="katalog-kapsayici katalog-ust-menu">
            <a href="{{ route('katalog.index') }}" class="katalog-marka">
                <span class="katalog-marka-isaret" aria-hidden="true">B2B</span>
                <span>
                    <span class="katalog-marka-adi">{{ __('panel.title') }}</span>
                    <span class="katalog-marka-aciklama">{{ __('katalog.title') }}</span>
                </span>
            </a>

            <div class="katalog-ust-araclar katalog-sepet-araclar">
                @include('katalog._sepet-baglantisi')
                <div class="katalog-dil">
                    @include('layouts._adminkit_language')
                </div>
            </div>
        </div>
    </header>

    <main id="ana-icerik" class="katalog-kapsayici katalog-icerik">
        @include('layouts._adminkit_alerts')
        @yield('content')
    </main>

    <footer class="katalog-alt-alan">
        <div class="katalog-kapsayici katalog-alt-icerik">
            <span>{{ __('panel.title') }}</span>
            <a href="{{ route('katalog.index') }}">{{ __('katalog.all-products') }}</a>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
