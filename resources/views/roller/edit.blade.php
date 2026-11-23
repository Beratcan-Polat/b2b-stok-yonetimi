@extends('layouts.adminkit')
@section('title', __('panel.edit-role'))
@section('content')
    <section class="mb-4">
        <h1 class="h3 mb-2">{{ __('panel.edit-role') }}</h1>
        <p class="text-muted mb-0">{{ __('panel.edit-role-description', ['role' => $rol->label]) }}</p>
    </section>
    <form action="{{ route('roller.update', $rol) }}" method="POST" class="card">
        @method('PUT')
        <div class="card-body">
            @include('roller._form', ['rol' => $rol, 'butonMetni' => __('panel.save-changes')])
        </div>
    </form>
@endsection
