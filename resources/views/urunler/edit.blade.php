@extends('layouts.adminkit')

@section('title', __('panel.edit-product'))

@section('content')
    <section class="mb-4">
        <h1 class="h3 mb-2">{{ __('panel.edit-product') }}</h1>
        <p class="text-muted mb-0">{{ __('panel.edit-product-description') }}</p>
    </section>

    <div class="row">
        <div class="col-12 col-lg-10 col-xl-8">
            <form action="{{ route('urunler.update', $urun) }}" method="POST" enctype="multipart/form-data" class="card">
                @csrf
                @method('PUT')

                <div class="card-body">
                    @include('urunler._form', ['urun' => $urun, 'butonMetni' => __('panel.save-changes')])
                </div>
            </form>
        </div>
    </div>
@endsection
