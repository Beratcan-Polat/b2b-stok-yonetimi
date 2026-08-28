@extends('layouts.app')

@section('title', 'Kategori Düzenle')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Kategori Düzenle</h1>
            <p>Kategori adını ve otomatik oluşturulan slug bilgisini güncelleyin.</p>
        </div>
    </section>

    <form
        action="{{ route('kategoriler.update', $kategori) }}"
        method="POST"
        class="form-kutusu form-dar"
    >
        @csrf
        @method('PUT')

        <div class="form-grup">
            <label for="name">Kategori Adı</label>

            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $kategori->name) }}"
                maxlength="255"
                required
                autofocus
            >
        </div>

        <div class="form-grup">
            <label>Mevcut Slug</label>

            <div class="salt-okunur-alan">
                {{ $kategori->slug }}
            </div>
        </div>

        <div class="form-butonlari">
            <button type="submit" class="buton buton-birincil">
                Değişiklikleri Kaydet
            </button>

            <a href="{{ route('kategoriler.index') }}" class="buton buton-ikincil">
                İptal
            </a>
        </div>
    </form>

@endsection