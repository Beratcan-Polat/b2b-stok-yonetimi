@if (session('success'))
    <div class="alert alert-success" role="status">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>{{ __('panel.fix-errors') }}</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $hata)
                <li>{{ $hata }}</li>
            @endforeach
        </ul>
    </div>
@endif
