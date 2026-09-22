@extends('layouts.app')

@section('title', 'Kullanıcıyı Düzenle')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Kullanıcıyı Düzenle</h1>
            <p>{{ $kullanici->email }} hesabının bilgilerini güncelleyin.</p>
        </div>
    </section>

    <form
        action="{{ route('kullanicilar.update', $kullanici) }}"
        method="POST"
        class="form-kutusu form-dar"
    >
        @csrf
        @method('PUT')

        <div class="form-grup">
            <label for="name">Ad Soyad</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $kullanici->name) }}"
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
                value="{{ old('email', $kullanici->email) }}"
                maxlength="255"
                required
            >
        </div>

        <div class="form-grup">
            <label for="role_id">Rol</label>

            <select
                id="role_id"
                name="role_id"
                required
                @disabled($kullanici->id === auth()->id())
            >
                @foreach ($roller as $rol)
                    <option
                        value="{{ $rol->id }}"
                        @selected(old('role_id', $kullanici->roles->first()?->id) == $rol->id)
                    >
                        {{ $rol->label }}
                    </option>
                @endforeach
            </select>

            @if ($kullanici->id === auth()->id())
                <small class="form-aciklama">
                    Kendi rolünüzü değiştiremezsiniz.
                </small>

                <input type="hidden" name="role_id" value="{{ $kullanici->roles->first()?->id }}">
            @endif
        </div>

        <div class="form-grup">
            <label for="password">Yeni Parola</label>

            <input
                type="password"
                id="password"
                name="password"
            >

            <small class="form-aciklama">
                Parolayı değiştirmek istemiyorsanız boş bırakın. En az 8 karakter.
            </small>
        </div>

        <div class="form-grup">
            <label for="password_confirmation">Yeni Parola Tekrarı</label>

            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
            >
        </div>

        <div class="form-butonlari">
            <button type="submit" class="buton buton-birincil">
                Değişiklikleri Kaydet
            </button>

            <a href="{{ route('kullanicilar.index') }}" class="buton buton-ikincil">
                İptal
            </a>
        </div>
    </form>

@endsection
