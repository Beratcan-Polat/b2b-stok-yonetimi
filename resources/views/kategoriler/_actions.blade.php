<div class="d-flex align-items-center flex-wrap gap-2">
    @can('update', $kategori)
        <a href="{{ route('kategoriler.edit', $kategori) }}"
            class="btn btn-secondary btn-sm">{{ __('panel.edit-category-button') }}</a>
    @endcan

    @can('delete', $kategori)
        <form action="{{ route('kategoriler.destroy', $kategori) }}" method="POST" class="m-0"
            data-confirm="{{ __('panel.confirm-delete-category') }}" onsubmit="return confirm(this.dataset.confirm)">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">{{ __('panel.delete-category-button') }}</button>
        </form>
    @endcan
</div>
