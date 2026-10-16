@if ($urun->image_path)
    <img src="{{ asset('storage/'.$urun->image_path) }}" alt="{{ $urun->name }}" class="urun-gorseli">
@else
    <div class="gorsel-yok">Görsel yok</div>
@endif
