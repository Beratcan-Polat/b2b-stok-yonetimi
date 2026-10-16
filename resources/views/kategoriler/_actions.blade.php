<div class="islem-alani">
    @can('update', $kategori)
        <a
            href="{{ route('kategoriler.edit', $kategori) }}"
            class="buton buton-ikincil buton-kucuk"
        >
            Düzenle
        </a>
    @endcan

    @can('delete', $kategori)
        <form
            action="{{ route('kategoriler.destroy', $kategori) }}"
            method="POST"
            class="satir-ici-form"
            onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')"
        >
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="buton buton-tehlike buton-kucuk"
            >
                Sil
            </button>
        </form>
    @endcan
</div>
