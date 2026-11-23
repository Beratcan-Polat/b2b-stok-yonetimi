@extends('layouts.katalog')

@section('title', __('katalog.title'))

@section('content')
    <section class="katalog-tanitim" aria-labelledby="katalog-baslik">
        <p class="katalog-ust-etiket">{{ __('katalog.title') }}</p>
        <h1 id="katalog-baslik">{{ __('katalog.heading') }}</h1>
        <p class="katalog-aciklama">{{ __('katalog.description') }}</p>
    </section>

    <form action="{{ route('katalog.index') }}" method="GET" class="katalog-filtreler" role="search" aria-label="{{ __('katalog.search-label') }}">
        <div class="katalog-arama-alani">
            <label for="arama" class="form-label">{{ __('katalog.search-label') }}</label>
            <input type="search" id="arama" name="arama" value="{{ $arama }}"
                class="form-control" placeholder="{{ __('katalog.search-placeholder') }}" maxlength="100">
        </div>

        <div class="katalog-kategori-alani">
            <label for="kategori" class="form-label">{{ __('panel.category') }}</label>
            <select id="kategori" name="kategori" class="form-select">
                <option value="">{{ __('panel.all-categories') }}</option>
                @foreach ($kategoriler as $kategori)
                    <option value="{{ $kategori->id }}" @selected((string) $kategoriId === (string) $kategori->id)>{{ $kategori->localizedName() }}</option>
                @endforeach
            </select>
        </div>

        <div class="katalog-siralama-alani">
            <label for="siralama" class="form-label">{{ __('katalog.sort') }}</label>
            <select id="siralama" name="siralama" class="form-select">
                @foreach (__('katalog.sort-options') as $deger => $etiket)
                    <option value="{{ $deger }}" @selected($siralama === $deger)>{{ $etiket }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary katalog-ara-dugmesi">{{ __('katalog.search') }}</button>
    </form>

    <section class="katalog-sonuclar" aria-labelledby="urunler-baslik">
        <div class="katalog-sonuc-basligi">
            <h2 id="urunler-baslik">{{ __('panel.products') }} <span class="katalog-sonuc-sayisi">{{ $urunler->total() }}</span></h2>

            @if ($arama !== '' || $kategoriId !== null)
                <a href="{{ route('katalog.index') }}" class="katalog-temizle">{{ __('katalog.clear-filters') }}</a>
            @else
                <span class="katalog-siralama">{{ __('katalog.sort-options.'.$siralama) }}</span>
            @endif
        </div>

        <div class="katalog-urunler">
            @forelse ($urunler as $urun)
                <article class="katalog-urun" aria-labelledby="urun-{{ $urun->id }}">
                    <div class="katalog-gorsel-alani">
                        <span @class(['katalog-stok-durumu', 'katalog-stok-yok' => (int) $urun->stock === 0])>
                            {{ (int) $urun->stock > 0 ? __('katalog.in-stock') : __('panel.out-of-stock') }}
                        </span>

                        @if ($urun->image_path)
                            <img src="{{ route('katalog.gorsel', ['urun' => $urun->id]) }}"
                                alt="{{ $urun->name }}"
                                class="katalog-urun-gorseli"
                                width="480"
                                height="360"
                                loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                decoding="async">
                        @else
                            <div class="katalog-gorsel-yok">
                                <span class="katalog-gorsel-isaret" aria-hidden="true">B2B</span>
                                <span>{{ __('panel.no-image') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="katalog-urun-bilgileri">
                        <p class="katalog-urun-kategori">{{ $urun->category?->localizedName() ?? __('panel.deleted-category') }}</p>
                        <h3 id="urun-{{ $urun->id }}" class="katalog-urun-adi">{{ $urun->name }}</h3>
                        <p class="katalog-urun-kodu">SKU: {{ $urun->sku }}</p>

                        <dl class="katalog-urun-alt">
                            <div>
                                <dt>{{ __('panel.price') }}</dt>
                                <dd class="katalog-fiyat">{{ number_format((float) $urun->price, 2, app()->getLocale() === 'tr' ? ',' : '.', app()->getLocale() === 'tr' ? '.' : ',') }} <span>₺</span></dd>
                            </div>
                            <div class="katalog-stok-adedi">
                                <dt>{{ __('panel.stock-quantity') }}</dt>
                                <dd>{{ $urun->stock }} <span>{{ __('panel.units') }}</span></dd>
                            </div>
                        </dl>
                    </div>
                    @include('katalog._sepete-ekle', ['urun' => $urun])
                </article>
            @empty
                <div class="katalog-bos-durum">
                    <span class="katalog-bos-isaret" aria-hidden="true">B2B</span>
                    <h3>{{ $arama !== '' || $kategoriId !== null ? __('panel.no-matching-products') : __('panel.no-products') }}</h3>
                    <p>{{ $arama !== '' || $kategoriId !== null ? __('katalog.empty-filter-help') : __('katalog.empty-help') }}</p>
                    @if ($arama !== '' || $kategoriId !== null)
                        <a href="{{ route('katalog.index') }}" class="btn btn-primary">{{ __('katalog.all-products') }}</a>
                    @endif
                </div>
            @endforelse
        </div>

        @if ($urunler->hasPages())
            <nav class="katalog-sayfalama" aria-label="{{ __('katalog.pagination') }}">
                <p class="katalog-sayfa-ozeti">{{ __('katalog.results', ['first' => $urunler->firstItem() ?? 0, 'last' => $urunler->lastItem() ?? 0, 'total' => $urunler->total()]) }}</p>
                <div class="katalog-sayfa-dugmeleri">
                    @if ($urunler->previousPageUrl())
                        <a href="{{ $urunler->previousPageUrl() }}" class="katalog-sayfa-dugmesi" rel="prev">{{ __('panel.datatable.paginate.previous') }}</a>
                    @else
                        <span class="katalog-sayfa-dugmesi" aria-disabled="true">{{ __('panel.datatable.paginate.previous') }}</span>
                    @endif

                    <span class="katalog-sayfa-numarasi" aria-current="page">{{ __('katalog.page', ['current' => $urunler->currentPage(), 'last' => $urunler->lastPage()]) }}</span>

                    @if ($urunler->nextPageUrl())
                        <a href="{{ $urunler->nextPageUrl() }}" class="katalog-sayfa-dugmesi" rel="next">{{ __('panel.datatable.paginate.next') }}</a>
                    @else
                        <span class="katalog-sayfa-dugmesi" aria-disabled="true">{{ __('panel.datatable.paginate.next') }}</span>
                    @endif
                </div>
            </nav>
        @endif
    </section>
@endsection
