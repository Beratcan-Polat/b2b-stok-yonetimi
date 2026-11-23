<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('panel.title'))</title>
    <link rel="stylesheet" href="{{ asset('adminkit/css/app.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/adminkit-ozel.css') }}">
</head>

<body class="d-flex flex-column min-vh-100">
    <a class="visually-hidden-focusable position-absolute p-2 bg-white" href="#ana-icerik">{{ __('panel.skip-content') }}</a>
    <header class="navbar navbar-light navbar-bg px-3 px-sm-4 py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 w-100">
            <a href="{{ route('giris') }}" class="navbar-brand fw-bold text-wrap m-0">{{ __('panel.title') }}</a>
            <div class="ms-auto">
                @include('layouts._adminkit_language')
            </div>
        </div>
    </header>

    <main id="ana-icerik" class="flex-grow-1 d-flex align-items-center py-4 py-md-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                    @include('layouts._adminkit_alerts')
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    @include('layouts._adminkit_footer')
    <script src="{{ asset('adminkit/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
