@php
    // Düzenlemede mevcut rolü, oluşturmada null değerini kullanır.
    $formRolu = $rol ?? null;

    // Düzenlenen rolün mevcut izin ID'lerini getirir.
    $mevcutIzinler = $formRolu
        ? $formRolu->permissions->modelKeys()
        : [];

    // Doğrulama hatasında gönderilen seçimlerin korunması için kullanılır.
    // Hiç checkbox seçilmediğinde de boş seçimleri ayırt etmemizi sağlar.
    $formGonderildi = old('izin_formu') === '1';

    // Hatalı form gönderildiyse gönderilen seçimleri kullanır.
    // İlk açılışta ise mevcut rolün izinlerini kullanır.
    $seciliIzinler = $formGonderildi
        ? old('permission_ids', [])
        : $mevcutIzinler;

    // Form değerleri metin gelebileceğinden ID'leri karşılaştırma için metne çevirir.
    $seciliIzinler = is_array($seciliIzinler)
        ? array_map('strval', array_filter($seciliIzinler, 'is_scalar'))
        : [];

    // İzin kodlarının ilk bölümünü okunabilir modül adlarıyla eşleştirir.
    $modulAdlari = [
        'kategori' => __('panel.categories'),
        'urun' => __('panel.products'),
        'siparis' => __('panel.orders'),
        'kullanici' => __('panel.users'),
        'tema' => __('tema.title'),
    ];
@endphp

<input type="hidden" name="izin_formu" value="1">

<div class="mb-3">
    <label for="label" class="form-label">{{ __('panel.role-name') }}</label>
    <input type="text" id="label" name="label" class="form-control" value="{{ old('label', $formRolu?->label) }}" maxlength="255" required autofocus>
    <div class="form-text">{{ __('panel.role-name-help') }}</div>
</div>

<div class="mb-4">
    <label for="name" class="form-label">{{ __('panel.role-code') }}</label>
    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $formRolu?->name) }}" maxlength="100" required>
    <div class="form-text">{{ __('panel.role-code-help') }}</div>
</div>

<section>
    <h2 class="h5 mb-2">{{ __('panel.role-permissions') }}</h2>
    <p class="text-muted mb-3">{{ __('panel.role-permissions-help') }}</p>

    @forelse ($izinGruplari as $modul => $izinler)
        <fieldset class="border rounded p-3 mb-3">
            <legend class="float-none w-auto px-2 fs-6 fw-bold">{{ $modulAdlari[$modul] ?? $modul }}</legend>

            @foreach ($izinler as $izin)
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" id="izin-{{ $izin->id }}" name="permission_ids[]" value="{{ $izin->id }}" @checked(in_array((string) $izin->id, $seciliIzinler, true))>
                    <label class="form-check-label" for="izin-{{ $izin->id }}">{{ data_get(__('panel.permissions'), $izin->name, $izin->label ?? $izin->name) }}</label>
                </div>
            @endforeach

        </fieldset>
    @empty
        <div class="alert alert-danger" role="alert">{{ __('panel.no-permissions') }}</div>
    @endforelse
</section>

<div class="d-flex align-items-center flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary">{{ $butonMetni }}</button>
    <a href="{{ route('roller.index') }}" class="btn btn-secondary">{{ __('panel.cancel-button') }}</a>
</div>
