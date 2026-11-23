@extends('layouts.adminkit')

@section('title', __('panel.new-user'))

@section('content')

    <section class="mb-4">
        <h1 class="h3 mb-2">{{ __('panel.new-user') }}</h1>
        <p class="text-muted mb-0">{{ __('panel.new-user-description') }}</p>
    </section>

    <div class="row">
        <div class="col-12 col-lg-10 col-xl-8">
            <form
                action="{{ route('kullanicilar.store') }}"
                method="POST"
                class="card"
            >
                @csrf

                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('panel.full-name') }}</label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
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
                            value="{{ old('email') }}"
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

                    @if (auth()->user()->isSuperAdmin())
                        <div class="mb-3">
                            <label for="role_mode" class="form-label">{{ __('panel.role-mode') }}</label>
                            <select id="role_mode" name="role_mode" class="form-select @error('role_mode') is-invalid @enderror"
                                aria-invalid="{{ $errors->has('role_mode') ? 'true' : 'false' }}"
                                @error('role_mode') aria-describedby="role-mode-error" @enderror>
                                <option value="existing" @selected(old('role_mode', 'existing') === 'existing')>{{ __('panel.select-existing-role') }}</option>
                                <option value="new" @selected(old('role_mode') === 'new')>{{ __('panel.create-new-role') }}</option>
                            </select>
                            @error('role_mode')
                                <div id="role-mode-error" class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <input type="hidden" name="role_mode" value="existing">
                    @endif

                    <div class="mb-4" data-existing-role>
                        <label for="role_id" class="form-label">{{ __('panel.role') }}</label>

                        <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror"
                            aria-invalid="{{ $errors->has('role_id') ? 'true' : 'false' }}"
                            @error('role_id') aria-describedby="role-error" @enderror required>
                            <option value="">{{ __('panel.select-role') }}</option>

                            @foreach ($roller as $rol)
                                <option
                                    value="{{ $rol->id }}"
                                    data-permissions='@json($rol->permissions->modelKeys())'
                                    data-super-admin="{{ $rol->isSuperAdmin() ? '1' : '0' }}"
                                    @selected(old('role_id') == $rol->id)
                                >
                                    {{ $rol->label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div id="role-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if (auth()->user()->isSuperAdmin())
                        @include('kullanicilar._new_role')
                    @endif

                    @include('kullanicilar._permissions')

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('panel.password') }}</label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            autocomplete="new-password"
                            minlength="8"
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                            aria-describedby="password-help @error('password') password-error @enderror"
                            required
                        >
                        @error('password')
                            <div id="password-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div id="password-help" class="form-text">
                            {{ __('panel.password-help') }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">{{ __('panel.password-confirmation') }}</label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control @error('password_confirmation') is-invalid @enderror"
                            autocomplete="new-password"
                            minlength="8"
                            aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                            @error('password_confirmation') aria-describedby="password-confirmation-error" @enderror
                            required
                        >
                        @error('password_confirmation')
                            <div id="password-confirmation-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('panel.save-user') }}
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
