<div class="d-flex align-items-center flex-wrap gap-2">
    @can('update', $kullanici)
        <a href="{{ route('kullanicilar.edit', $kullanici) }}" class="btn btn-secondary btn-sm">{{ __('panel.edit') }}</a>
    @endcan

    @can('admin')
        @if ($kullanici->id !== auth()->id() && ! $kullanici->isSuperAdmin())
            <form action="{{ route('admin.impersonate.start', $kullanici) }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">{{ __('panel.login') }}</button>
            </form>
        @endif
    @endcan

    @can('delete', $kullanici)
        @if ($kullanici->id !== auth()->id())
            <form action="{{ route('kullanicilar.destroy', $kullanici) }}" method="POST" class="m-0" data-confirm="{{ __('panel.confirm-delete-user') }}" onsubmit="return confirm(this.dataset.confirm)">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">{{ __('panel.delete') }} </button>
            </form>
        @endif
    @endcan
</div>
