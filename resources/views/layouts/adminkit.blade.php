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

<body>
    <a class="visually-hidden-focusable position-absolute p-2 bg-white" href="#ana-icerik">{{ __('panel.skip-content') }}</a>
    <div class="wrapper">
        @include('layouts._adminkit_sidebar')
        <div class="main">
            @include('layouts._adminkit_navbar')
            <main id="ana-icerik" class="content">
                <div class="container-fluid p-0">
                    @include('layouts._adminkit_alerts')
                    @yield('content')
                </div>
            </main>
            @include('layouts._adminkit_footer')
        </div>
    </div>
    <script src="{{ asset('adminkit/js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
