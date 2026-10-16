<div class="islem-alani">
    @can('update', $kullanici)
        <a
            href="{{ route('kullanicilar.edit', $kullanici) }}"
            class="buton buton-ikincil buton-kucuk"
        >
            Düzenle
        </a>
    @endcan

    @can('admin')
        @if ($kullanici->id !== auth()->id() && ! $kullanici->isSuperAdmin())
            <form
                action="{{ route('admin.impersonate.start', $kullanici) }}"
                method="POST"
                class="satir-ici-form"
            >
                @csrf

                <button type="submit" class="buton buton-ikincil buton-kucuk">
                    Giriş Yap
                </button>
            </form>
        @endif
    @endcan

    @can('delete', $kullanici)
        @if ($kullanici->id !== auth()->id())
            <form
                action="{{ route('kullanicilar.destroy', $kullanici) }}"
                method="POST"
                class="satir-ici-form"
                onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')"
            >
                @csrf

                @method('DELETE')

                <button type="submit" class="buton buton-tehlike buton-kucuk">
                    Sil
                </button>
            </form>
        @endif
    @endcan
</div>
