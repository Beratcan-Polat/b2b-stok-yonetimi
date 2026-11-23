<form action="{{ route('sepet.store', ['urun' => $urun->id]) }}" method="POST" class="katalog-sepete-ekle">
    @csrf

    <input type="hidden" name="quantity" value="1">

    <button
        type="submit"
        class="katalog-sepet-dugmesi katalog-sepet-dugmesi--ekle"
        @disabled((int) $urun->stock < 1)
    >
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 3h2l2.4 12h11.2L21 7H6" />
            <path d="M12 8v6m-3-3h6" />
            <circle cx="9" cy="20" r="1" /><circle cx="18" cy="20" r="1" />
        </svg>
        <span>{{ (int) $urun->stock > 0 ? __('sepet.add') : __('panel.out-of-stock') }}</span>
    </button>
</form>
