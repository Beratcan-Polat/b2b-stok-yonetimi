@extends('layouts.adminkit')

@section('title', __('tema.title'))

@section('content')
    <div class="row">
        <div class="col-12 col-lg-8">
            <h1 class="h3 mb-3">{{ __('tema.title') }}</h1>

            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4">
                        {{ __('tema.description') }}
                    </p>

                    <form action="{{ route('tema.update') }}" method="POST">

                        @csrf


                        @method('PUT')

                        <div class="mb-4">
                            <label for="theme" class="form-label">
                                {{ __('tema.select') }}
                            </label>

                            <select
                                id="theme"
                                name="theme"
                                class="form-select @error('theme') is-invalid @enderror"
                                aria-invalid="{{ $errors->has('theme') ? 'true' : 'false' }}"
                                @error('theme') aria-describedby="theme-error" @enderror
                                required
                            >
                                @foreach ($temalar as $kod => $etiket)

                                    <option
                                        value="{{ $kod }}"
                                        @selected(old('theme', $aktifTema) === $kod)
                                    >
                                        {{ __($etiket) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('theme')
                                <div id="theme-error" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ __('tema.save') }}
                            </button>


                            <a
                                href="{{ route('katalog.index') }}"
                                class="btn btn-outline-secondary"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                {{ __('tema.open-catalog') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
