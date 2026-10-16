@extends('layouts.app')

@section('title', 'Profilim')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Profilim</h1>
            <p>Hesap bilgilerinizi görüntüleyin ve parolanızı değiştirin.</p>
        </div>
    </section>

    <div class="form-kutusu form-dar">
        <section aria-labelledby="hesap-bilgileri-baslik">
            <h2 id="hesap-bilgileri-baslik">Hesap Bilgileri</h2>

            <div class="form-grup">
                <strong>Ad Soyad</strong>
                <div class="salt-okunur-alan">{{ auth()->user()->name }}</div>
            </div>

            <div class="form-grup">
                <strong>E-posta</strong>
                <div class="salt-okunur-alan">{{ auth()->user()->email }}</div>
            </div>
        </section>

        <hr>

        <section aria-labelledby="parola-degistir-baslik">
            <h2 id="parola-degistir-baslik">Parolamı Değiştir</h2>

            @if (session()->has('impersonator_id'))
                <p class="salt-okunur-alan">
                    Başka bir kullanıcı hesabındayken parola değiştirilemez.
                    Süper admin hesabınıza dönerek kullanıcı düzenleme ekranını kullanabilirsiniz.
                </p>
            @else
                <form action="{{ route('profil.sifre-guncelle') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="form-grup">
                        <label for="current_password">Mevcut Parola</label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <div class="form-grup">
                        <label for="password">Yeni Parola</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >
                        <small class="form-aciklama">Yeni parola en az 8 karakter olmalıdır.</small>
                    </div>

                    <div class="form-grup">
                        <label for="password_confirmation">Yeni Parola Tekrarı</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >
                    </div>

                    <div class="form-butonlari">
                        <button type="submit" class="buton buton-birincil">
                            Parolayı Değiştir
                        </button>
                    </div>
                </form>
            @endif
        </section>
    </div>

@endsection
