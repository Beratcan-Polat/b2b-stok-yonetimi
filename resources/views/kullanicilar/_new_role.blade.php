@php
    $modulAdlari = ['kategori' => 'Kategoriler', 'urun' => 'Ürünler', 'siparis' => 'Siparişler', 'kullanici' => 'Kullanıcılar'];
    $seciliIzinler = collect(old('new_role_permission_ids', []))->filter(fn ($id) => is_scalar($id))->map(fn ($id) => (string) $id)->all();
@endphp

<fieldset class="kullanici-yetki-alanlari" data-new-role-fields hidden disabled>
    <div class="form-grup">
        <label for="new_role_name">Yeni Rol Adı</label>
        <input type="text" id="new_role_name" name="new_role_name" value="{{ old('new_role_name') }}" maxlength="255">
        <small class="form-aciklama">Rol kodu bu addan otomatik oluşturulur. Örnek: Depo Sorumlusu → depo-sorumlusu.</small>
    </div>

    <h2>Yeni Rolün Yetkileri</h2>
    <p class="form-aciklama">Buradaki seçimler role aittir; bu role sonradan bağlanan kullanıcılar da aynı yetkileri alır.</p>

    @foreach ($izinGruplari as $modul => $izinler)
        <fieldset class="kullanici-yetki-grubu">
            <legend>{{ $modulAdlari[$modul] ?? $modul }}</legend>

            @foreach ($izinler as $izin)
                <label class="kullanici-yetki-satiri" for="yeni-rol-izin-{{ $izin->id }}">
                    <input type="checkbox" id="yeni-rol-izin-{{ $izin->id }}" name="new_role_permission_ids[]" value="{{ $izin->id }}" @checked(in_array((string) $izin->id, $seciliIzinler, true))>
                    <span>{{ $izin->label ?? $izin->name }}</span>
                </label>
            @endforeach
        </fieldset>
    @endforeach
</fieldset>
