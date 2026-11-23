@extends('layouts.adminkit')

@section('title', __('panel.profile'))

@section('content')
    <section class="mb-4">
        <h1 class="h3 mb-2">{{ __('panel.profile') }}</h1>
        <p class="text-muted mb-0">{{ __('panel.profile-description') }}</p>
    </section>

    <div class="row">
        <div class="col-12 col-lg-10 col-xl-8">
            <section class="card" aria-labelledby="hesap-bilgileri-baslik">
                <div class="card-body">
                    <h2 id="hesap-bilgileri-baslik" class="h5 mb-4">{{ __('panel.account-details') }}</h2>
                    <dl class="row g-3 mb-0">
                        <div class="col-12 col-md-6">
                            <dt class="text-muted fw-normal mb-1">{{ __('panel.full-name') }}</dt>
                            <dd class="mb-0 fw-bold text-break">{{ auth()->user()->name }}</dd>
                        </div>
                        <div class="col-12 col-md-6">
                            <dt class="text-muted fw-normal mb-1">{{ __('panel.email') }}</dt>
                            <dd class="mb-0 fw-bold text-break">{{ auth()->user()->email }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <section class="card" aria-labelledby="parola-degistir-baslik">
                <div class="card-body">
                    <h2 id="parola-degistir-baslik" class="h5 mb-4">{{ __('panel.change-my-password') }}</h2>

                    @if (session()->has('impersonator_id'))
                        <div class="alert alert-info mb-0" role="status">{{ __('panel.impersonation-password-help') }}</div>
                    @else
                        <form action="{{ route('profil.sifre-guncelle') }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">{{ __('panel.current-password') }}</label>
                                <input
                                    type="password"
                                    id="current_password"
                                    name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    autocomplete="current-password"
                                    aria-invalid="{{ $errors->has('current_password') ? 'true' : 'false' }}"
                                    @error('current_password') aria-describedby="current-password-error" @enderror
                                    required
                                >
                                @error('current_password')
                                    <div id="current-password-error" class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
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
                                        required
                                    >
                                    @error('password')
                                        <div id="password-error" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div id="password-help" class="form-text">{{ __('panel.new-password-help') }}</div>
                                </div>
                                <div class="col-12 col-md-6 mb-4">
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
                                        required
                                    >
                                    @error('password_confirmation')
                                        <div id="password-confirmation-error" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('panel.change-password') }}</button>
                        </form>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
