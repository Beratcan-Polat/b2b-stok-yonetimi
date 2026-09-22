@extends('layouts.app')

@section('title', 'Yeni Kullanıcı Ekle')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Yeni Kullanıcı Ekle</h1>
            <p>Kullanıcı bilgilerini girin ve panel içindeki rolünü belirleyin.</p>
        </div>
    </section>

    <form
        action="{{ route('kullanicilar.store') }}"
        method="POST"
        class="form-kutusu form-dar"
    >
        @csrf

        <div class="form-grup">
            <label for="name">Ad Soyad</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                maxlength="255"
                required
                autofocus
            >
        </div>

        <div class="form-grup">
            <label for="email">E-posta</label>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                maxlength="255"
                required
            >
        </div>

        <div class="form-grup">
            <label for="role_id">Rol</label>

            <select id="role_id" name="role_id" required>
                <option value="">Rol seçin</option>

                @foreach ($roller as $rol)
                    <option
                        value="{{ $rol->id }}"
                        @selected(old('role_id') == $rol->id)
                    >
                        {{ $rol->label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-grup">
            <label for="password">Parola</label>

            <input
                type="password"
                id="password"
                name="password"
                required
            >

            <small class="form-aciklama">
                En az 8 karakter olmalıdır.
            </small>
        </div>

        <div class="form-grup">
            <label for="password_confirmation">Parola Tekrarı</label>

            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                required
            >
        </div>

        <div class="form-butonlari">
            <button type="submit" class="buton buton-birincil">
                Kullanıcıyı Kaydet
            </button>

            <a href="{{ route('kullanicilar.index') }}" class="buton buton-ikincil">
                İptal
            </a>
        </div>
    </form>

@endsection
