@extends('layouts.adminkit')

@section('title', __('panel.new-product'))

@section('content')
    <section class="mb-4">
        <h1 class="h3 mb-2">{{ __('panel.new-product') }}</h1>
        <p class="text-muted mb-0">{{ __('panel.new-product-description') }}</p>
    </section>

    <div class="row">
        <div class="col-12 col-lg-10 col-xl-8">
            <form action="{{ route('urunler.store') }}" method="POST" enctype="multipart/form-data" class="card">
                @csrf

                <div class="card-body">
                    @include('urunler._form', ['butonMetni' => __('panel.save-product')])
                </div>
            </form>
        </div>
    </div>
@endsection
