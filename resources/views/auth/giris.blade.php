@extends('layouts.app')

@section('title', 'Giriş Yap')

@section('content')

    <div class="giris-ekrani">
        <div class="giris-kutusu">

            <div class="giris-baslik">
                <h1>Giriş Yap</h1>
                <p>Panele erişmek için giriş bilgilerinizi girin.</p>
            </div>

            <form action="{{ route('giris.gonder') }}" method="POST">
                @csrf

                <div class="form-grup">
                    <label for="email">E-posta</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>

                <div class="form-grup">
                    <label for="password">Parola</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <label class="hatirla">
                    <input type="checkbox" name="remember" value="1">
                    Beni hatırla
                </label>

                <button type="submit" class="buton buton-birincil">
                    Giriş Yap
                </button>
            </form>

        </div>
    </div>

@endsection
