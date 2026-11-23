@extends('layouts.adminkit')

@section('title', __('panel.quick-order'))

@section('content')

    <section class="mb-4">
        <h1 class="h3 mb-2">{{ __('panel.quick-order') }}</h1>
        <p class="text-muted mb-0">{{ __('panel.order-description') }}</p>
    </section>

    <div class="row">
        <div class="col-12 col-lg-10 col-xl-8">
            <section class="card" aria-labelledby="order-product-name">
                <div class="card-body">
                    <h2 id="order-product-name" class="h5 mb-4">{{ $urun->name }}</h2>
                    <dl class="row g-3 mb-0">
                        <div class="col-12 col-sm-4">
                            <dt class="text-muted fw-normal mb-1">SKU</dt>
                            <dd class="mb-0 fw-bold text-break">{{ $urun->sku }}</dd>
                        </div>
                        <div class="col-12 col-sm-4">
                            <dt class="text-muted fw-normal mb-1">{{ __('panel.unit-price') }}</dt>
                            <dd class="mb-0 fw-bold">
                                {{ number_format($urun->price, 2, app()->getLocale() === 'tr' ? ',' : '.', app()->getLocale() === 'tr' ? '.' : ',') }} ₺
                            </dd>
                        </div>
                        <div class="col-12 col-sm-4">
                            <dt class="text-muted fw-normal mb-1">{{ __('panel.available-stock') }}</dt>
                            <dd class="mb-0 fw-bold">{{ $urun->stock }} {{ __('panel.units') }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <form action="{{ route('siparisler.store', $urun) }}" method="POST" class="card">
                @csrf

                <div class="card-body">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">{{ __('panel.customer-name') }}</label>
                        <input
                            type="text"
                            id="customer_name"
                            name="customer_name"
                            class="form-control @error('customer_name') is-invalid @enderror"
                            value="{{ old('customer_name') }}"
                            maxlength="255"
                            autocomplete="name"
                            aria-invalid="{{ $errors->has('customer_name') ? 'true' : 'false' }}"
                            @error('customer_name') aria-describedby="customer-name-error" @enderror
                            required
                            autofocus
                        >
                        @error('customer_name')
                            <div id="customer-name-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="quantity" class="form-label">{{ __('panel.order-quantity') }}</label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            class="form-control @error('quantity') is-invalid @enderror"
                            value="{{ old('quantity', 1) }}"
                            min="1"
                            max="{{ $urun->stock }}"
                            step="1"
                            aria-invalid="{{ $errors->has('quantity') ? 'true' : 'false' }}"
                            aria-describedby="quantity-help @error('quantity') quantity-error @enderror"
                            required
                        >
                        @error('quantity')
                            <div id="quantity-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="quantity-help" class="form-text">
                            {{ __('panel.order-limit-help', ['stock' => $urun->stock]) }}
                        </div>
                    </div>

                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">{{ __('panel.submit-order') }}</button>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Product::class) ? route('urunler.index') : route('siparisler.urun-sec') }}" class="btn btn-secondary">
                            {{ __('panel.cancel-button') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
