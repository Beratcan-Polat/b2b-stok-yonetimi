@extends('layouts.adminkit')

@section('title', __('panel.home'))

@section('content')
    <section class="mb-4">
        <h1 class="h3 mb-2">{{ __('panel.panel') }}</h1>
        <p class="text-muted mb-0">
            {{ __('panel.welcome') }} {{ auth()->user()->name }}. {{ __('panel.admin_sentence') }}
        </p>
    </section>

    <div class="row g-4">
        @canany(['viewAny', 'create'], App\Models\Category::class)
            <div class="col-12 col-md-6 col-xl-4">
                <article class="card h-100 mb-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded p-3 mb-3 align-self-start">
                            <i data-feather="folder" aria-hidden="true"></i>
                        </div>
                        <h2 class="h4 mb-2">{{ __('panel.categories-management') }}</h2>
                        <p class="text-muted mb-4">{{ __('panel.categories-sentence') }}</p>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Category::class) ? route('kategoriler.index') : route('kategoriler.create') }}" class="btn btn-primary mt-auto align-self-start">
                            {{ auth()->user()->can('viewAny', App\Models\Category::class) ? __('panel.manage-categories') : __('panel.add-category') }}
                        </a>
                    </div>
                </article>
            </div>
        @endcanany

        @canany(['viewAny', 'create', 'viewTrashed'], App\Models\Product::class)
            <div class="col-12 col-md-6 col-xl-4">
                <article class="card h-100 mb-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded p-3 mb-3 align-self-start">
                            <i data-feather="package" aria-hidden="true"></i>
                        </div>
                        <h2 class="h4 mb-2">{{ __('panel.products-management') }}</h2>
                        <p class="text-muted mb-4">{{ __('panel.products-sentence') }}</p>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Product::class) ? route('urunler.index') : (auth()->user()->can('create', App\Models\Product::class) ? route('urunler.create') : route('urunler.silinenler')) }}" class="btn btn-primary mt-auto align-self-start">
                            {{ auth()->user()->can('viewAny', App\Models\Product::class) ? __('panel.manage-products') : (auth()->user()->can('create', App\Models\Product::class) ? __('panel.add-product') : __('panel.deleted-products')) }}
                        </a>
                    </div>
                </article>
            </div>
        @endcanany

        @canany(['viewAny', 'create'], App\Models\Order::class)
            <div class="col-12 col-md-6 col-xl-4">
                <article class="card h-100 mb-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded p-3 mb-3 align-self-start">
                            <i data-feather="shopping-cart" aria-hidden="true"></i>
                        </div>
                        <h2 class="h4 mb-2">{{ __('panel.orders-management') }}</h2>
                        <p class="text-muted mb-4">{{ __('panel.orders-sentence') }}</p>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Order::class) ? route('siparisler.index') : route('siparisler.urun-sec') }}" class="btn btn-primary mt-auto align-self-start">
                            {{ auth()->user()->can('viewAny', App\Models\Order::class) ? __('panel.view-orders') : __('panel.create-order') }}
                        </a>
                    </div>
                </article>
            </div>
        @endcanany

        @canany(['viewAny', 'create'], App\Models\User::class)
            <div class="col-12 col-md-6 col-xl-4">
                <article class="card h-100 mb-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded p-3 mb-3 align-self-start">
                            <i data-feather="users" aria-hidden="true"></i>
                        </div>
                        <h2 class="h4 mb-2">{{ __('panel.users-management') }}</h2>
                        <p class="text-muted mb-4">{{ __('panel.users-sentence') }}</p>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\User::class) ? route('kullanicilar.index') : route('kullanicilar.create') }}" class="btn btn-primary mt-auto align-self-start">
                            {{ auth()->user()->can('viewAny', App\Models\User::class) ? __('panel.manage-users') : __('panel.add-user') }}
                        </a>
                    </div>
                </article>
            </div>
        @endcanany

        @can('viewAny', App\Models\Role::class)
            <div class="col-12 col-md-6 col-xl-4">
                <article class="card h-100 mb-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded p-3 mb-3 align-self-start">
                            <i data-feather="shield" aria-hidden="true"></i>
                        </div>
                        <h2 class="h4 mb-2">{{ __('panel.roles-management') }}</h2>
                        <p class="text-muted mb-4">{{ __('panel.roles-sentence') }}</p>
                        <a href="{{ route('roller.index') }}" class="btn btn-primary mt-auto align-self-start">
                            {{ __('panel.manage-roles') }}
                        </a>
                    </div>
                </article>
            </div>
        @endcan

        <div class="col-12 col-md-6 col-xl-4">
            <article class="card h-100 mb-0">
                <div class="card-body d-flex flex-column">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light text-primary rounded p-3 mb-3 align-self-start">
                        <i data-feather="user" aria-hidden="true"></i>
                    </div>
                    <h2 class="h4 mb-2">{{ __('panel.profile') }}</h2>
                    <p class="text-muted mb-4">{{ __('panel.profile-description') }}</p>
                    <a href="{{ route('profil.goster') }}" class="btn btn-primary mt-auto align-self-start">
                        {{ __('panel.profile') }}
                    </a>
                </div>
            </article>
        </div>
    </div>
@endsection
