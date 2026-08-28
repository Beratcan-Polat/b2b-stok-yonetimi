@extends('layouts.app')

@section('title', 'Kategori Yönetimi')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Kategori Yönetimi</h1>
            <p>Ürünlerin bağlı olacağı kategorileri görüntüleyin ve yönetin.</p>
        </div>

        <a href="{{ route('kategoriler.create') }}" class="buton buton-birincil">
            Yeni Kategori Ekle
        </a>
    </section>

    <div class="tablo-kapsayici">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kategori Adı</th>
                    <th>Slug</th>
                    <th>Ürün Sayısı</th>
                    <th>İşlemler</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($kategoriler as $kategori)
                    <tr>
                        <td>{{ $kategori->id }}</td>
                        <td>{{ $kategori->name }}</td>

                        <td>
                            <span class="kod-metin">
                                {{ $kategori->slug }}
                            </span>
                        </td>

                        <td>{{ $kategori->products_count }}</td>

                        <td>
                            <div class="islem-alani">
                                <a
                                    href="{{ route('kategoriler.edit', $kategori) }}"
                                    class="buton buton-ikincil buton-kucuk"
                                >
                                    Düzenle
                                </a>

                                <form
                                    action="{{ route('kategoriler.destroy', $kategori) }}"
                                    method="POST"
                                    class="satir-ici-form"
                                    onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="buton buton-tehlike buton-kucuk"
                                    >
                                        Sil
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="bos-kayit">
                            Henüz kategori bulunmuyor.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection