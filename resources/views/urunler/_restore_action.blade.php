@can('restore', App\Models\Product::class)
    <form action="{{ route('urunler.geri-yukle', $urun->id) }}" method="POST"
        onsubmit="return confirm('Bu ürünü geri yüklemek istediğinize emin misiniz?')">
        @csrf
        @method('PATCH')

        <button type="submit" class="buton buton-birincil buton-kucuk">Geri Yükle</button>
    </form>
@endcan
