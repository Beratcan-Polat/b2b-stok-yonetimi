@if (auth()->user()->isSuperAdmin())
    @php
        $modulAdlari = ['kategori' => 'Kategoriler', 'urun' => 'Ürünler', 'siparis' => 'Siparişler', 'kullanici' => 'Kullanıcılar'];
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

    <section class="kullanici-yetkileri" data-user-permissions data-overrides='@json($kayitliSecimler)'>
        <h2>Kullanıcıya Özel Yetkiler</h2>
        <p class="form-aciklama">
            Bu seçimler yalnızca bu kullanıcıyı etkiler. Aynı roldeki diğer kullanıcıların yetkileri değişmez.
            Rolden gelen yetkiler işaretlidir. Ek yetki vermek için işaretleyin, bu kullanıcıdan kaldırmak için işareti kaldırın.
            Her yetki bağımsızdır; birini seçmek başka bir yetkiyi otomatik açmaz.
        </p>

        <p class="form-aciklama" data-super-admin-note hidden>
            Süper Admin bütün yetkilere sahiptir; kişisel kısıtlama uygulanmaz.
        </p>

        <fieldset class="kullanici-yetki-alanlari" data-override-fields>
            <input type="hidden" name="user_permissions_present" value="1">
            @foreach ($izinGruplari as $modul => $izinler)
                <fieldset class="kullanici-yetki-grubu">
                    <legend>{{ $modulAdlari[$modul] ?? $modul }}</legend>

                    @foreach ($izinler as $izin)
                        @php
                            $durum = $kayitliSecimler[$izin->id] ?? 'inherit';
                            $isaretli = $eskiCheckboxlar
                                ? in_array((string) $izin->id, $secilenIzinler, true)
                                : ($durum === 'allow' || ($durum === 'inherit' && in_array($izin->id, $rolIzinleri)));
                        @endphp
                        <label class="kullanici-yetki-satiri" for="kullanici-izin-{{ $izin->id }}">
                            <input type="checkbox" id="kullanici-izin-{{ $izin->id }}" name="user_permission_ids[]" value="{{ $izin->id }}" @checked($isaretli)>
                            <span>{{ $izin->label ?? $izin->name }}</span>
                        </label>
                    @endforeach
                </fieldset>
            @endforeach
        </fieldset>
    </section>

    <script src="{{ asset('js/user-permissions.js') }}" defer></script>
@endif
