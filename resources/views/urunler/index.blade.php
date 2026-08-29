@extends('layouts.app')

@section('title', 'Ürün Yönetimi')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Ürün Yönetimi</h1>
            <p>Ürün bilgilerini, görsellerini, fiyatlarını ve stoklarını yönetin.</p>
        </div>

        <a href="{{ route('urunler.create') }}" class="buton buton-birincil">
            Yeni Ürün Ekle
        </a>
    </section>

    <div class="tablo-kapsayici">
        <table>
            <thead>
                <tr>
                    <th>Görsel</th>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th>Fiyat</th>
                    <th>Stok</th>
                    <th>İşlemler</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($urunler as $urun)
                    <tr>
                        <td>
                            @if ($urun->image_path)
                                <img
                                    src="{{ asset('storage/' . $urun->image_path) }}"
                                    alt="{{ $urun->name }}"
                                    class="urun-gorseli"
                                >
                            @else
                                <div class="gorsel-yok">Görsel yok</div>
                            @endif
                        </td>

                        <td>
                            <strong>{{ $urun->name }}</strong>
                            <div class="urun-alt-bilgi">
                                SKU: {{ $urun->sku }}
                            </div>
                        </td>

                        <td>{{ $urun->category->name }}</td>

                        <td>
                            {{ number_format($urun->price, 2, ',', '.') }} ₺
                        </td>

                        <td>
                            @if ($urun->stock > 0)
                                <span class="stok-etiketi stok-var">
                                    {{ $urun->stock }} adet
                                </span>
                            @else
                                <span class="stok-etiketi stok-yok">
                                    Stokta Yok
                                </span>
                            @endif
                        </td>

                        <td>
                            <div class="islem-alani">
                                <a
                                    href="{{ route('urunler.edit', $urun) }}"
                                    class="buton buton-ikincil buton-kucuk"
                                >
                                    Düzenle
                                </a>

                                <form
                                    action="{{ route('urunler.destroy', $urun) }}"
                                    method="POST"
                                    class="satir-ici-form"
                                    onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')"
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
                        <td colspan="6" class="bos-kayit">
                            Henüz ürün bulunmuyor.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($urunler->hasPages())
        <div class="sayfalama">
            @if ($urunler->onFirstPage())
                <span class="sayfalama-pasif">Önceki</span>
            @else
                <a href="{{ $urunler->previousPageUrl() }}">Önceki</a>
            @endif

            <span>
                Sayfa {{ $urunler->currentPage() }} / {{ $urunler->lastPage() }}
            </span>

            @if ($urunler->hasMorePages())
                <a href="{{ $urunler->nextPageUrl() }}">Sonraki</a>
            @else
                <span class="sayfalama-pasif">Sonraki</span>
            @endif
        </div>
    @endif

@endsection