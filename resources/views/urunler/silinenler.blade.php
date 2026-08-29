@extends('layouts.app')

@section('title', 'Silinen Ürünler')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Silinen Ürünler</h1>
            <p>Soft Delete ile silinen ürünleri görüntüleyin ve geri yükleyin.</p>
        </div>

        <a href="{{ route('urunler.index') }}" class="buton buton-ikincil">
            Ürünlere Dön
        </a>
    </section>

    <div class="tablo-kapsayici">
        <table>
            <thead>
                <tr>
                    <th>Görsel</th>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Silinme Tarihi</th>
                    <th>İşlem</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($urunler as $urun)
                    <tr class="silinen-satir">
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
                        <td>{{ $urun->stock }} adet</td>

                        <td>
                            {{ $urun->deleted_at->format('d.m.Y H:i') }}
                        </td>

                        <td>
                            <form
                                action="{{ route('urunler.geri-yukle', $urun->id) }}"
                                method="POST"
                                onsubmit="return confirm('Bu ürünü geri yüklemek istediğinize emin misiniz?')"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="buton buton-birincil buton-kucuk"
                                >
                                    Geri Yükle
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="bos-kayit">
                            Silinen ürün bulunmuyor.
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
                Sayfa {{ $urunler->currentPage() }}
                / {{ $urunler->lastPage() }}
            </span>

            @if ($urunler->hasMorePages())
                <a href="{{ $urunler->nextPageUrl() }}">Sonraki</a>
            @else
                <span class="sayfalama-pasif">Sonraki</span>
            @endif
        </div>
    @endif

@endsection
