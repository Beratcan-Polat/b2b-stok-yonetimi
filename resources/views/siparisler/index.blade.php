@extends('layouts.app')

@section('title', 'Siparişler')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Siparişler</h1>
            <p>Oluşturulan siparişleri ve toplam tutarlarını görüntüleyin.</p>
        </div>
    </section>

    <div class="tablo-kapsayici">
        <table>
            <thead>
                <tr>
                    <th>Sipariş No</th>
                    <th>Ürün</th>
                    <th>Müşteri</th>
                    <th>Adet</th>
                    <th>Toplam Tutar</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($siparisler as $siparis)
                    <tr>
                        <td>#{{ $siparis->id }}</td>

                        <td>
                            <strong>{{ $siparis->product->name }}</strong>

                            <div class="urun-alt-bilgi">
                                {{ $siparis->product->sku }}
                            </div>
                        </td>

                        <td>{{ $siparis->customer_name }}</td>
                        <td>{{ $siparis->quantity }}</td>

                        <td class="tutar">
                            {{ number_format($siparis->total_price, 2, ',', '.') }} ₺
                        </td>

                        <td>
                            <span class="durum-etiketi durum-bekliyor">
                                {{ $siparis->status }}
                            </span>
                        </td>

                        <td>
                            {{ $siparis->created_at->format('d.m.Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="bos-kayit">
                            Henüz sipariş oluşturulmadı.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($siparisler->hasPages())
        <div class="sayfalama">
            @if ($siparisler->onFirstPage())
                <span class="sayfalama-pasif">Önceki</span>
            @else
                <a href="{{ $siparisler->previousPageUrl() }}">Önceki</a>
            @endif

            <span>
                Sayfa {{ $siparisler->currentPage() }}
                / {{ $siparisler->lastPage() }}
            </span>

            @if ($siparisler->hasMorePages())
                <a href="{{ $siparisler->nextPageUrl() }}">Sonraki</a>
            @else
                <span class="sayfalama-pasif">Sonraki</span>
            @endif
        </div>
    @endif

@endsection
