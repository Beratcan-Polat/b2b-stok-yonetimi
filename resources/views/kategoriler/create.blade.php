@extends('layouts.app')

@section('title', 'Yeni Kategori Ekle')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Yeni Kategori Ekle</h1>
            <p>Kategori adını girin. Slug bilgisi otomatik oluşturulacaktır.</p>
        </div>
    </section>

    <form
        action="{{ route('kategoriler.store') }}"
        method="POST"
        class="form-kutusu form-dar"
    >
        @csrf

        <div class="form-grup">
            <label for="name">Kategori Adı</label>

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

        <div class="form-butonlari">
            <button type="submit" class="buton buton-birincil">
                Kategoriyi Kaydet
            </button>

            <a href="{{ route('kategoriler.index') }}" class="buton buton-ikincil">
                İptal
            </a>
        </div>
    </form>

@endsection