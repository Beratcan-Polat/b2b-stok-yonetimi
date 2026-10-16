<div class="islem-alani">
    @can('update', $rol)
        <a href="{{ route('roller.edit', $rol) }}" class="buton buton-ikincil buton-kucuk">
            Düzenle
        </a>
    @endcan

    @can('delete', $rol)
        @if ($rol->users_count > 0)
            <span class="urun-alt-bilgi">Kullanıcı atanmış; silinemez</span>
        @else
            <form action="{{ route('roller.destroy', $rol) }}" method="POST" class="satir-ici-form"
                onsubmit="return confirm('Bu rolü silmek istediğinize emin misiniz?')">
                @csrf
                @method('DELETE')

                <button type="submit" class="buton buton-tehlike buton-kucuk">Sil</button>
            </form>
        @endif
    @endcan

    @if ($rol->isSuperAdmin())
        <span class="urun-alt-bilgi">Değiştirilemez</span>
    @endif
</div>
