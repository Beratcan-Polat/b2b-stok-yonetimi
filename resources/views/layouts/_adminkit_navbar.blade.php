<nav class="navbar navbar-expand navbar-light navbar-bg" aria-label="{{ __('panel.main-menu') }}">
    <button type="button" class="sidebar-toggle js-sidebar-toggle btn btn-link border-0 p-0" aria-controls="sidebar" aria-label="{{ __('panel.menu') }}">
        <i class="hamburger align-self-center" aria-hidden="true"></i>
    </button>
    <div class="d-flex align-items-center justify-content-end gap-3 ms-auto">
        @include('layouts._adminkit_language')
        <div class="nav-item dropdown">
            <button type="button" id="kullanici-menusu" class="nav-link dropdown-toggle d-flex align-items-center border-0 bg-transparent p-0"
                data-bs-toggle="dropdown" aria-expanded="false" title="{{ auth()->user()->name }}">
                <span class="avatar rounded bg-light text-primary d-none d-sm-inline-flex align-items-center justify-content-center me-2" aria-hidden="true">
                    <i data-feather="user"></i>
                </span>
                <span class="text-dark text-truncate tema-kullanici-adi">{{ auth()->user()->name }}</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="kullanici-menusu">
                <a class="dropdown-item" href="{{ route('profil.goster') }}">
                    <i class="align-middle me-2" data-feather="user" aria-hidden="true"></i>
                    <span class="align-middle">{{ __('panel.profile') }}</span>
                </a>
                <div class="dropdown-divider"></div>
                @if (session()->has('impersonator_id'))
                    <form action="{{ route('admin.impersonate.return') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="align-middle me-2" data-feather="arrow-left" aria-hidden="true"></i>
                            <span class="align-middle">{{ __('panel.return_to_admin') }}</span>
                        </button>
                    </form>
                @else
                    <form action="{{ route('cikis') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="align-middle me-2" data-feather="log-out" aria-hidden="true"></i>
                            <span class="align-middle">{{ __('panel.logout') }}</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</nav>
