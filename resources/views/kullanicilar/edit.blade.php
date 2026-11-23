@extends('layouts.adminkit')

@section('title', __('panel.edit-user'))

@section('content')

    <section class="mb-4">
        <h1 class="h3 mb-2">{{ __('panel.edit-user') }}</h1>
        <p class="text-muted mb-0">{{ __('panel.edit-user-description', ['email' => $kullanici->email]) }}</p>
    </section>

    <div class="row">
        <div class="col-12 col-lg-10 col-xl-8">
            <form
                action="{{ route('kullanicilar.update', $kullanici) }}"
                method="POST"
                class="card"
            >
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('panel.full-name') }}</label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $kullanici->name) }}"
                            maxlength="255"
                            autocomplete="name"
                            aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                            @error('name') aria-describedby="name-error" @enderror
                            required
                            autofocus
                        >
                        @error('name')
                            <div id="name-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('panel.email') }}</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $kullanici->email) }}"
                            maxlength="255"
                            autocomplete="email"
                            aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                            @error('email') aria-describedby="email-error" @enderror
                            required
                        >
                        @error('email')
                            <div id="email-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="role_id" class="form-label">{{ __('panel.role') }}</label>

                        <select
                            id="role_id"
                            name="role_id"
                            class="form-select @error('role_id') is-invalid @enderror"
                            aria-invalid="{{ $errors->has('role_id') ? 'true' : 'false' }}"
                            aria-describedby="{{ $kullanici->id === auth()->id() ? 'own-role-help' : '' }} @error('role_id') role-error @enderror"
                            required
                            @disabled($kullanici->id === auth()->id())
                        >
                            @foreach ($roller as $rol)
                                <option
                                    value="{{ $rol->id }}"
                                    data-permissions='@json($rol->permissions->modelKeys())'
                                    data-super-admin="{{ $rol->isSuperAdmin() ? '1' : '0' }}"
                                    @selected(old('role_id', $kullanici->roles->first()?->id) == $rol->id)
                                >
                                    {{ $rol->label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div id="role-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if ($kullanici->id === auth()->id())
                            <div id="own-role-help" class="form-text">
                                {{ __('panel.own-role-help') }}
                            </div>

                            <input type="hidden" name="role_id" value="{{ $kullanici->roles->first()?->id }}">
                        @endif
                    </div>

                    @include('kullanicilar._permissions')

                    @if (auth()->user()->isSuperAdmin())
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('panel.new-password') }}</label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="new-password"
                                minlength="8"
                                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                aria-describedby="password-help @error('password') password-error @enderror"
                            >
                            @error('password')
                                <div id="password-error" class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div id="password-help" class="form-text">
                                {{ __('panel.optional-password-help') }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">{{ __('panel.new-password-confirmation') }}</label>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                autocomplete="new-password"
                                minlength="8"
                                aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                                @error('password_confirmation') aria-describedby="password-confirmation-error" @enderror
                            >
                            @error('password_confirmation')
                                <div id="password-confirmation-error" class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('panel.save-changes') }}
                        </button>

                        <a href="{{ auth()->user()->can('viewAny', App\Models\User::class) ? route('kullanicilar.index') : route('anasayfa') }}" class="btn btn-secondary">
                            {{ __('panel.cancel-button') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
