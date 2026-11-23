@if ($urun->image_path)
    <img src="{{ asset('storage/'.$urun->image_path) }}" alt="{{ $urun->name }}" class="tema-urun-gorseli rounded border" loading="lazy">
@else
    <div class="tema-urun-gorseli d-flex align-items-center justify-content-center rounded border bg-light text-muted small text-center p-1">{{ __('panel.no-image') }}</div>
@endif
