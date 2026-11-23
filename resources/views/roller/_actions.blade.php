<div class="d-flex align-items-center flex-wrap gap-2">
    @can('update', $rol)
        <a href="{{ route('roller.edit', $rol) }}"
            class="btn btn-secondary btn-sm">{{ __('panel.edit-role') }}</a>
    @endcan

    @can('delete', $rol)
        @if ($rol->users_count > 0)
            <span class="small text-muted">{{ __('panel.role-has-users') }}</span>
        @else
            <form action="{{ route('roller.destroy', $rol) }}" method="POST" class="m-0" data-confirm="{{ __('panel.confirm-delete-role') }}" onsubmit="return confirm(this.dataset.confirm)">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">{{ __('panel.delete') }}</button>
            </form>
        @endif
    @endcan

    @if ($rol->isSuperAdmin())
        <span class="badge bg-secondary">{{ __('panel.immutable-role') }}</span>
    @endif
</div>
