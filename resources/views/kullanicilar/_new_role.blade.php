@php
    $modulAdlari = ['kategori' => __('panel.categories'), 'urun' => __('panel.products'), 'siparis' => __('panel.orders'), 'kullanici' => __('panel.users'), 'tema' => __('tema.title')];
    $seciliIzinler = collect(old('new_role_permission_ids', []))->filter(fn ($id) => is_scalar($id))->map(fn ($id) => (string) $id)->all();
@endphp

<fieldset class="border-0 p-0 m-0" data-new-role-fields hidden disabled>
    <div class="mb-3">
        <label for="new_role_name" class="form-label">{{ __('panel.new-role-name') }}</label>
        <input type="text" id="new_role_name" name="new_role_name"
            class="form-control {{ $errors->hasAny(['new_role_name', 'new_role_slug']) ? 'is-invalid' : '' }}"
            value="{{ old('new_role_name') }}" maxlength="255"
            aria-invalid="{{ $errors->hasAny(['new_role_name', 'new_role_slug']) ? 'true' : 'false' }}"
            aria-describedby="new-role-help {{ $errors->hasAny(['new_role_name', 'new_role_slug']) ? 'new-role-error' : '' }}">
        @if ($errors->hasAny(['new_role_name', 'new_role_slug']))
            <div id="new-role-error" class="invalid-feedback">{{ $errors->first('new_role_name') ?: $errors->first('new_role_slug') }}</div>
        @endif
        <div id="new-role-help" class="form-text">{{ __('panel.new-role-code-help') }}</div>
    </div>

    <h2 class="h5 mb-2">{{ __('panel.new-role-permissions') }}</h2>
    <p class="text-muted mb-3">{{ __('panel.new-role-permissions-help') }}</p>

    <div class="row g-3 mb-4">
        @foreach ($izinGruplari as $modul => $izinler)
            <div class="col-12 col-md-6">
                <fieldset class="border rounded p-3 h-100">
                    <legend class="float-none w-auto px-2 fs-6 fw-bold">{{ $modulAdlari[$modul] ?? $modul }}</legend>

                    @foreach ($izinler as $izin)
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="yeni-rol-izin-{{ $izin->id }}" name="new_role_permission_ids[]" value="{{ $izin->id }}" @checked(in_array((string) $izin->id, $seciliIzinler, true))>
                            <label class="form-check-label" for="yeni-rol-izin-{{ $izin->id }}">{{ data_get(__('panel.permissions'), $izin->name, $izin->label ?? $izin->name) }}</label>
                        </div>
                    @endforeach
                </fieldset>
            </div>
        @endforeach
    </div>
</fieldset>
