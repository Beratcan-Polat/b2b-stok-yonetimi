@if (auth()->user()->isSuperAdmin())
    @php
        $modulAdlari = ['kategori' => __('panel.categories'), 'urun' => __('panel.products'), 'siparis' => __('panel.orders'), 'kullanici' => __('panel.users'), 'tema' => __('tema.title')];
        $kayitliSecimler = isset($kullanici)
            ? $kullanici->permissions->mapWithKeys(fn ($izin) => [$izin->id => $izin->pivot->allowed ? 'allow' : 'deny'])->all()
            : [];
        $eskiSecimler = old('permission_overrides');
        if (is_array($eskiSecimler)) {
            $kayitliSecimler = [];
            foreach ($eskiSecimler as $secim) {
                if (is_array($secim) && isset($secim['permission_id'], $secim['state']) && is_scalar($secim['permission_id']) && is_string($secim['state'])) {
                    $kayitliSecimler[$secim['permission_id']] = $secim['state'];
                }
            }
        }
        $secilenRol = $roller->firstWhere('id', old('role_id', isset($kullanici) ? $kullanici->roles->first()?->id : null));
        $rolIzinleri = $secilenRol?->permissions->modelKeys() ?? [];
        $eskiCheckboxlar = old('user_permissions_present') !== null;
        $secilenIzinler = collect(old('user_permission_ids', []))->filter(fn ($id) => is_scalar($id))->map(fn ($id) => (string) $id)->all();
    @endphp

    <section class="mb-4 border-top pt-4" data-user-permissions data-overrides='@json($kayitliSecimler)'>
        <h2 class="h5 mb-2">{{ __('panel.user-permissions') }}</h2>
        <p class="text-muted mb-3">
            {{ __('panel.user-permissions-help') }}
        </p>

        <p class="alert alert-info mb-0" data-super-admin-note hidden>
            {{ __('panel.super-admin-permissions-help') }}
        </p>

        <fieldset class="border-0 p-0 m-0" data-override-fields>
            <input type="hidden" name="user_permissions_present" value="1">
            <div class="row g-3">
                @foreach ($izinGruplari as $modul => $izinler)
                    <div class="col-12 col-md-6">
                        <fieldset class="border rounded p-3 h-100">
                            <legend class="float-none w-auto px-2 fs-6 fw-bold">{{ $modulAdlari[$modul] ?? $modul }}</legend>

                            @foreach ($izinler as $izin)
                                @php
                                    $durum = $kayitliSecimler[$izin->id] ?? 'inherit';
                                    $isaretli = $eskiCheckboxlar
                                        ? in_array((string) $izin->id, $secilenIzinler, true)
                                        : ($durum === 'allow' || ($durum === 'inherit' && in_array($izin->id, $rolIzinleri)));
                                @endphp
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input" id="kullanici-izin-{{ $izin->id }}" name="user_permission_ids[]" value="{{ $izin->id }}" @checked($isaretli)>
                                    <label class="form-check-label" for="kullanici-izin-{{ $izin->id }}">{{ data_get(__('panel.permissions'), $izin->name, $izin->label ?? $izin->name) }}</label>
                                </div>
                            @endforeach
                        </fieldset>
                    </div>
                @endforeach
            </div>
        </fieldset>
    </section>

    <script src="{{ asset('js/user-permissions.js') }}" defer></script>
@endif
