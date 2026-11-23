@extends('layouts.adminkit')

@section('title', __('panel.edit-category'))

@section('content')

    <section class="mb-4">
        <h1 class="h3 mb-2">{{ __('panel.edit-category') }}</h1>
        <p class="text-muted mb-0">{{ __('panel.edit-category-sentence') }}</p>
    </section>

    <div class="row">
        <div class="col-12 col-lg-8 col-xl-6">
            <form action="{{ route('kategoriler.update', $kategori) }}" method="POST" class="card">
                @csrf
                @method('PUT')

                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('panel.category-name-turkish') }}</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $kategori->name) }}"
                            maxlength="255"
                            aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                            @error('name') aria-describedby="name-error" @enderror
                            required
                            autofocus
                        >
                        @error('name')
                            <div id="name-error" class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('kategoriler._english_name', ['kategori' => $kategori])

                    <div class="mb-4">
                        <label for="slug" class="form-label">{{ __('panel.slug') }}</label>
                        <input type="text" id="slug" class="form-control bg-light" value="{{ $kategori->slug }}" readonly>
                    </div>

                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('panel.edit-category-button') }}
                        </button>
                        <a href="{{ auth()->user()->can('viewAny', App\Models\Category::class) ? route('kategoriler.index') : route('anasayfa') }}" class="btn btn-secondary">
                            {{ __('panel.cancel-button') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
