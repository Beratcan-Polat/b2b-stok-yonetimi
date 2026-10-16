<div class="islem-alani">
    @can('create', App\Models\Order::class)
        @if ($urun->stock > 0)
            <a href="{{ route('siparisler.create', $urun) }}" class="buton buton-birincil buton-kucuk">
                Hızlı Sipariş Ver
            </a>
        @else
            <span class="buton buton-devre-disi buton-kucuk">Hızlı Sipariş Ver</span>
        @endif
    @endcan

    @can('update', $urun)
        <a href="{{ route('urunler.edit', $urun) }}" class="buton buton-ikincil buton-kucuk">
            Düzenle
        </a>
    @endcan

    @can('delete', $urun)
        <form action="{{ route('urunler.destroy', $urun) }}" method="POST" class="satir-ici-form"
            onsubmit="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">
            @csrf
            @method('DELETE')

            <button type="submit" class="buton buton-tehlike buton-kucuk">Sil</button>
        </form>
    @endcan
</div>
