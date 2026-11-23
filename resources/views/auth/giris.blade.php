@extends('layouts.adminkit-guest')

@section('title', __('panel.login'))

@section('content')
    <div class="card mb-0">
        <div class="card-body p-4 p-sm-5">
            <div class="text-center mb-4">
                <h1 class="h3 mb-2">{{ __('panel.login') }}</h1>
                <p class="text-muted mb-0">{{ __('panel.login_sentence') }}</p>
            </div>

            <form action="{{ route('giris.gonder') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('panel.login_email') }}</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                        @error('email') aria-describedby="email-error" @enderror
                        required
                        autofocus
                    >
                    @error('email')
                        <div id="email-error" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('panel.login_password') }}</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                        autocomplete="current-password"
                        aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        @error('password') aria-describedby="password-error" @enderror
                        required
                    >
                    @error('password')
                        <div id="password-error" class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input type="checkbox" id="remember" name="remember" value="1" class="form-check-input" @checked(old('remember'))>
                    <label for="remember" class="form-check-label">{{ __('panel.login_remember') }}</label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100">{{ __('panel.login_submit') }}</button>
            </form>
        </div>
    </div>
@endsection
