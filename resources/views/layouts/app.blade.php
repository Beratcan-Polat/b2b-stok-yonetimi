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
                <a href="{{ route('anasayfa') }}">Ana Sayfa</a>
                <a href="{{ route('kategoriler.index') }}">Kategoriler</a>
                <span class="pasif-menu">Ürünler</span>
                <span class="pasif-menu">Siparişler</span>
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