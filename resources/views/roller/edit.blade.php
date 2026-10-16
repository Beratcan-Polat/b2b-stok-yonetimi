{{-- Projenin ortak sayfa düzenini kullanır. --}}
@extends('layouts.app')

{{-- Tarayıcı sekmesinin başlığını belirler. --}}
@section('title', 'Rolü Düzenle')

{{-- Sayfanın içerik alanını başlatır. --}}
@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Rolü Düzenle</h1>

            {{-- Düzenlenen rolün adını gösterir. --}}
            <p>
                {{ $rol->label }} rolünün bilgilerini ve yetkilerini güncelleyin.
                Yetki değişiklikleri bu role bağlı bütün kullanıcıları etkiler.
            </p>
        </div>
    </section>

    {{-- Rol bilgilerini update metoduna gönderir. --}}
    <form
        action="{{ route('roller.update', $rol) }}"
        method="POST"
        class="form-kutusu"
    >
        {{-- İsteğin CSRF token'ını ekler. --}}
        @csrf

        {{-- HTML formunun POST isteğini Laravel'de PUT olarak işler. --}}
        @method('PUT')

        {{-- Mevcut rolü ortak forma açıkça gönderir. --}}
        @include('roller._form', [
            'rol' => $rol,
            'butonMetni' => 'Değişiklikleri Kaydet',
        ])
    </form>

@endsection
