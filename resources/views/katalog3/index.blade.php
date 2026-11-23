@extends('layouts.katalog3')

@section('title', __('katalog.title'))

@section('content')
    @php($vitrinUrunu = $urunler->getCollection()->first(fn (\App\Models\Product $urun): bool => filled($urun->image_path)))

    <section class="magaza-tanitim" aria-labelledby="magaza-baslik">
        <div class="magaza-tanitim-metni">
            <p class="magaza-ust-etiket">{{ __('katalog.title') }}</p>
            <h1 id="magaza-baslik">{{ __('katalog.heading') }}</h1>
            <p class="magaza-aciklama">{{ __('katalog.description') }}</p>
            <a href="#urunler-baslik" class="magaza-kesfet">{{ __('katalog.all-products') }} <span aria-hidden="true">→</span></a>
        </div>

        <div class="magaza-vitrin">
            @if ($vitrinUrunu)
                <a href="#urun-{{ $vitrinUrunu->id }}" class="magaza-vitrin-baglantisi">
                    <div class="magaza-vitrin-gorsel">
                        <img src="{{ route('katalog.gorsel', ['urun' => $vitrinUrunu->id]) }}"
                            alt="{{ $vitrinUrunu->name }}"
                            width="400"
                            height="300"
                            decoding="async">
                    </div>
                    <div class="magaza-vitrin-aciklama">
                        <span>{{ $vitrinUrunu->name }}</span>
                        <strong>{{ number_format((float) $vitrinUrunu->price, 2, app()->getLocale() === 'tr' ? ',' : '.', app()->getLocale() === 'tr' ? '.' : ',') }} ₺</strong>
                    </div>
                </a>
            @else
                <div class="magaza-vitrin-bos">
                    <span aria-hidden="true">B2B</span>
                    <p>{{ __('katalog.title') }}</p>
                </div>
            @endif
        </div>
    </section>

    <section class="magaza-sonuclar" aria-labelledby="urunler-baslik">
        <div class="magaza-liste-basligi">
            <div class="magaza-liste-ozeti">
                <h2 id="urunler-baslik">{{ __('panel.products') }} <span>{{ $urunler->total() }}</span></h2>
                @if ($arama !== '' || $kategoriId !== null)
                    <a href="{{ route('katalog.index') }}" class="magaza-temizle">{{ __('katalog.clear-filters') }}</a>
                @endif
            </div>

            <form action="{{ route('katalog.index') }}" method="GET" class="magaza-filtreler" aria-label="{{ __('katalog.title') }}">
                <input type="hidden" name="arama" value="{{ $arama }}">
                <input type="hidden" name="kategori" value="{{ $kategoriId }}">
                <div>
                    <label for="siralama" class="form-label">{{ __('katalog.sort') }}</label>
                    <select id="siralama" name="siralama" class="form-select">
                        @foreach (__('katalog.sort-options') as $deger => $etiket)
                            <option value="{{ $deger }}" @selected($siralama === $deger)>{{ $etiket }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="magaza-dugme">{{ __('katalog.search') }}</button>
            </form>
        </div>

        <div class="magaza-urunler">
            @forelse ($urunler as $urun)
                <article class="magaza-urun" aria-labelledby="urun-{{ $urun->id }}">
                    <div class="magaza-gorsel-alani">
                        <span @class(['magaza-stok-durumu', 'magaza-stok-yok' => (int) $urun->stock === 0])>
                            {{ (int) $urun->stock > 0 ? __('katalog.in-stock') : __('panel.out-of-stock') }}
                        </span>
                        @if ($urun->image_path)
                            <img src="{{ route('katalog.gorsel', ['urun' => $urun->id]) }}"
                                alt="{{ $urun->name }}"
                                class="magaza-urun-gorseli"
                                width="480"
                                height="360"
                                loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                                decoding="async">
                        @else
                            <div class="magaza-gorsel-yok">
                                <span aria-hidden="true">B2B</span>
                                <p>{{ __('panel.no-image') }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="magaza-urun-bilgileri">
                        <p class="magaza-urun-kategori">{{ $urun->category?->localizedName() ?? __('panel.deleted-category') }}</p>
                        <h3 id="urun-{{ $urun->id }}">{{ $urun->name }}</h3>
                        <p class="magaza-urun-kodu">SKU: {{ $urun->sku }}</p>
                        <dl class="magaza-urun-alt">
                            <div>
                                <dt>{{ __('panel.price') }}</dt>
                                <dd class="magaza-fiyat">{{ number_format((float) $urun->price, 2, app()->getLocale() === 'tr' ? ',' : '.', app()->getLocale() === 'tr' ? '.' : ',') }} <span>₺</span></dd>
                            </div>
                            <div class="magaza-stok-adedi">
                                <dt>{{ __('panel.stock-quantity') }}</dt>
                                <dd>{{ $urun->stock }} <span>{{ __('panel.units') }}</span></dd>
                            </div>
                        </dl>
                    </div>
                    @include('katalog._sepete-ekle', ['urun' => $urun])
                </article>
            @empty
                <div class="magaza-bos-durum">
                    <span class="magaza-bos-isaret" aria-hidden="true">B2B</span>
                    <h3>{{ $arama !== '' || $kategoriId !== null ? __('panel.no-matching-products') : __('panel.no-products') }}</h3>
                    <p>{{ $arama !== '' || $kategoriId !== null ? __('katalog.empty-filter-help') : __('katalog.empty-help') }}</p>
                    @if ($arama !== '' || $kategoriId !== null)
                        <a href="{{ route('katalog.index') }}" class="magaza-dugme">{{ __('katalog.all-products') }}</a>
                    @endif
                </div>
            @endforelse
        </div>

        @if ($urunler->hasPages())
            <nav class="magaza-sayfalama" aria-label="{{ __('katalog.pagination') }}">
                <p>{{ __('katalog.results', ['first' => $urunler->firstItem() ?? 0, 'last' => $urunler->lastItem() ?? 0, 'total' => $urunler->total()]) }}</p>
                <div class="magaza-sayfa-dugmeleri">
                    @if ($urunler->previousPageUrl())
                        <a href="{{ $urunler->previousPageUrl() }}" class="magaza-sayfa-dugmesi" rel="prev">{{ __('panel.datatable.paginate.previous') }}</a>
                    @else
                        <span class="magaza-sayfa-dugmesi" aria-disabled="true">{{ __('panel.datatable.paginate.previous') }}</span>
                    @endif
                    <span class="magaza-sayfa-numarasi" aria-current="page">{{ __('katalog.page', ['current' => $urunler->currentPage(), 'last' => $urunler->lastPage()]) }}</span>
                    @if ($urunler->nextPageUrl())
                        <a href="{{ $urunler->nextPageUrl() }}" class="magaza-sayfa-dugmesi" rel="next">{{ __('panel.datatable.paginate.next') }}</a>
                    @else
                        <span class="magaza-sayfa-dugmesi" aria-disabled="true">{{ __('panel.datatable.paginate.next') }}</span>
                    @endif
                </div>
            </nav>
        @endif
    </section>
@endsection
