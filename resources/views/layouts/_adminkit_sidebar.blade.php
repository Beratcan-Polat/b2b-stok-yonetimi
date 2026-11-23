<nav id="sidebar" class="sidebar js-sidebar" aria-label="{{ __('panel.panel-menu') }}">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="{{ route('anasayfa') }}">
            <span class="align-middle">{{ __('panel.title') }}</span>
        </a>
        <ul class="sidebar-nav">
            <li class="sidebar-header">{{ __('panel.panel') }}</li>
            <li @class(['sidebar-item', 'active' => request()->routeIs('anasayfa')])>
                <a class="sidebar-link" href="{{ route('anasayfa') }}">
                    <i class="align-middle" data-feather="home" aria-hidden="true"></i>
                    <span class="align-middle">{{ __('panel.home') }}</span>
                </a>
            </li>

            @canany(['viewAny', 'create'], App\Models\Category::class)
                <li @class(['sidebar-item', 'active' => request()->routeIs('kategoriler.*')])> {{-- Kategori listesi veya formu açıldığında ilgili menü öğesini aktif gösterir. --}}
                    <a class="sidebar-link"
                        href="{{ auth()->user()->can('viewAny', App\Models\Category::class) ? route('kategoriler.index') : route('kategoriler.create') }}">
                        <i class="align-middle" data-feather="folder" aria-hidden="true"></i>
                        <span class="align-middle">{{ __('panel.categories') }}</span>
                    </a>
                </li>
            @endcanany

            @canany(['viewAny', 'create', 'viewTrashed'], App\Models\Product::class)
                <li @class(['sidebar-item', 'active' => request()->routeIs('urunler.*')])>
                    <a class="sidebar-link"
                        href="{{ auth()->user()->can('viewAny', App\Models\Product::class) ? route('urunler.index') : (auth()->user()->can('create', App\Models\Product::class) ? route('urunler.create') : route('urunler.silinenler')) }}">
                        <i class="align-middle" data-feather="package" aria-hidden="true"></i>
                        <span class="align-middle">{{ __('panel.products') }}</span>
                    </a>
                </li>
            @endcanany

            @canany(['viewAny', 'create'], App\Models\Order::class)
                <li @class(['sidebar-item', 'active' => request()->routeIs('siparisler.*')])>
                    <a class="sidebar-link"
                        href="{{ auth()->user()->can('viewAny', App\Models\Order::class) ? route('siparisler.index') : route('siparisler.urun-sec') }}">
                        <i class="align-middle" data-feather="shopping-cart" aria-hidden="true"></i>
                        <span class="align-middle">{{ __('panel.orders') }}</span>
                    </a>
                </li>
            @endcanany
            @canany(['viewAny', 'create'], App\Models\User::class)
                <li @class(['sidebar-item', 'active' => request()->routeIs('kullanicilar.*')])>
                    <a class="sidebar-link"
                        href="{{ auth()->user()->can('viewAny', App\Models\User::class) ? route('kullanicilar.index') : route('kullanicilar.create') }}">
                        <i class="align-middle" data-feather="users" aria-hidden="true"></i>
                        <span class="align-middle">{{ __('panel.users') }}</span>
                    </a>
                </li>
            @endcanany
            @can('viewAny', App\Models\Role::class)
                <li @class(['sidebar-item', 'active' => request()->routeIs('roller.*')])>
                    <a class="sidebar-link" href="{{ route('roller.index') }}">
                        <i class="align-middle" data-feather="shield" aria-hidden="true"></i>
                        <span class="align-middle">{{ __('panel.roles') }}</span>
                    </a>
                </li>
            @endcan

            @can('tema.yonet')
                <li @class([
                    'sidebar-item',
                    'active' => request()->routeIs('tema.*'),
                ])>
                    <a class="sidebar-link" href="{{ route('tema.edit') }}">
                        <i
                            class="align-middle"
                            data-feather="layout"
                            aria-hidden="true"
                        ></i>

                        <span class="align-middle">
                            {{ __('tema.title') }}
                        </span>
                    </a>
                </li>
            @endcan
        </ul>
    </div>
</nav>
