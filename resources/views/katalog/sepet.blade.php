@extends('layouts.katalog')

@section('title', __('sepet.title'))

@section('content')
    <section>
        <h1>{{ __('sepet.title') }}</h1>

        <p>
            <a href="{{ route('katalog.index') }}">
                {{ __('sepet.continue') }}
            </a>
        </p>

        {{-- Aktarım başarısızsa session verisi durur; kullanıcı tekrar deneyebilir. --}}
        @if ($hasPendingGuestCart)
            <form action="{{ route('sepet.merge') }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-warning">
                    {{ __('sepet.retry') }}
                </button>
            </form>
        @endif

        @if ($items->isEmpty())
            <p>{{ __('sepet.empty') }}</p>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('sepet.product') }}</th>
                            <th>{{ __('sepet.price') }}</th>
                            <th>{{ __('sepet.quantity') }}</th>
                            <th>{{ __('sepet.subtotal') }}</th>
                            <th>{{ __('sepet.remove') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $item)
                            @php
                                // Ürün tamamen silinmişse null olabilir.
                                $product = $item['product'];

                                $canUpdate = $product !== null
                                    && ! $product->trashed()
                                    && (int) $product->stock > 0;

                                $needsReview = ! $canUpdate
                                    || $item['quantity'] > (int) $product->stock;

                                $subtotal = (float) ($product?->price ?? 0)
                                    * $item['quantity'];
                            @endphp

                            <tr>
                                <td>
                                    {{ $product?->name ?? __('sepet.deleted') }}

                                    @if ($needsReview)
                                        <p class="text-danger">
                                            {{ __('sepet.unavailable') }}
                                        </p>
                                    @endif
                                </td>

                                <td>
                                    {{ number_format((float) ($product?->price ?? 0), 2, ',', '.') }} ₺
                                </td>

                                <td>
                                    @if ($canUpdate)
                                        <form
                                            action="{{ route('sepet.update', ['urun' => $item['product_id']]) }}"
                                            method="POST"
                                        >
                                            @csrf

                                            {{-- HTML formu POST gönderir; Laravel bunu PATCH olarak işler. --}}
                                            @method('PATCH')

                                            <input
                                                type="number"
                                                name="quantity"
                                                value="{{ $item['quantity'] }}"
                                                min="1"
                                                max="{{ $product->stock }}"
                                                aria-label="{{ __('sepet.quantity') }}"
                                                required
                                            >

                                            <button type="submit" class="btn btn-primary">
                                                {{ __('sepet.update') }}
                                            </button>
                                        </form>
                                    @else
                                        {{ $item['quantity'] }}
                                    @endif
                                </td>

                                <td>
                                    {{ number_format($subtotal, 2, ',', '.') }} ₺
                                </td>

                                <td>
                                    {{-- Ürün silinmiş olsa da kimliğiyle sepetten çıkarılabilir. --}}
                                    <form
                                        action="{{ route('sepet.destroy', ['urun' => $item['product_id']]) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-danger">
                                            {{ __('sepet.remove') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p>
                <strong>
                    {{ __('sepet.total') }}:
                    {{ number_format($total, 2, ',', '.') }} ₺
                </strong>
            </p>

            <form action="{{ route('sepet.clear') }}" method="POST">
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger">
                    {{ __('sepet.clear') }}
                </button>
            </form>
        @endif

        @guest
            {{-- Bu hedef auth gerektirir; girişten sonra sepete geri dönülür. --}}
            <p>
                <a href="{{ route('sepet.account') }}" class="btn btn-primary">
                    {{ __('sepet.login') }}
                </a>
            </p>
        @endguest
    </section>
@endsection