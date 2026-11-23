<a href="{{ route('sepet.index') }}" class="katalog-sepet-dugmesi katalog-sepet-dugmesi--baglanti" @if (request()->routeIs('sepet.*')) aria-current="page" @endif>
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M3 3h2l2.4 12h11.2L21 7H6" />
        <circle cx="9" cy="20" r="1" /><circle cx="18" cy="20" r="1" />
    </svg>
    <span>{{ __('sepet.title') }}</span>
</a>
