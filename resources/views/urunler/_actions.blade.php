<div class="d-flex align-items-center flex-wrap gap-2">
    @can('create', App\Models\Order::class)
        @if ($urun->stock > 0)
            <a href="{{ route('siparisler.create', $urun) }}" class="btn btn-primary btn-sm text-nowrap">
                {{ __('panel.quick-order') }}
            </a>
        @else
            <button type="button" class="btn btn-primary btn-sm text-nowrap" disabled>{{ __('panel.quick-order') }}</button>
        @endif
    @endcan

    @can('update', $urun)
        <a href="{{ route('urunler.edit', $urun) }}" class="btn btn-secondary btn-sm">
            {{ __('panel.edit') }}
        </a>
    @endcan

    @can('delete', $urun)
        <form action="{{ route('urunler.destroy', $urun) }}" method="POST" class="m-0"
            data-confirm="{{ __('panel.confirm-delete-product') }}" onsubmit="return confirm(this.dataset.confirm)">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-danger btn-sm">{{ __('panel.delete') }}</button>
        </form>
    @endcan
</div>
