{{-- Projenin ortak sayfa düzenini kullanır. --}}
@extends('layouts.app')

{{-- Tarayıcı sekmesinin başlığını belirler. --}}
@section('title', 'Yeni Rol Ekle')

{{-- Sayfanın içerik alanını başlatır. --}}
@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Yeni Rol Ekle</h1>

            <p>
                Rol bilgilerini girin ve bu role verilecek yetkileri seçin.
            </p>
        </div>
    </section>

    {{-- Yeni rol bilgilerini store metoduna gönderir. --}}
    <form
        action="{{ route('roller.store') }}"
        method="POST"
        class="form-kutusu"
    >
        {{-- Laravel'in form isteğini doğrulaması için CSRF token'ı ekler. --}}
        @csrf

        {{-- Ortak rol formunu ekler ve kaydetme butonunun metnini belirler. --}}
        @include('roller._form', [
            'butonMetni' => 'Rolü Oluştur',
        ])
    </form>

@endsection
