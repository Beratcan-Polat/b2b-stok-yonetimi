@extends('layouts.adminkit')

@section('title', __('panel.new-role'))

@section('content')

    <section class="mb-4">
        <h1 class="h3 mb-2">{{ __('panel.new-role') }}</h1>
        <p class="text-muted mb-0">{{ __('panel.new-role-description') }}</p>
    </section>

    <form action="{{ route('roller.store') }}" method="POST" class="card">
        @csrf

        <div class="card-body">
            @include('roller._form', ['butonMetni' => __('panel.create-role')])
        </div>
    </form>

@endsection
