@can('restore', App\Models\Product::class)
    <form action="{{ route('urunler.geri-yukle', $urun->id) }}" method="POST" class="m-0"
        data-confirm="{{ __('panel.confirm-restore-product') }}" onsubmit="return confirm(this.dataset.confirm)">
        @csrf
        @method('PATCH')

        <button type="submit" class="btn btn-primary btn-sm text-nowrap">{{ __('panel.restore') }}</button>
    </form>
@endcan
