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
        'kategori' => 'Kategoriler',
        'urun' => 'Ürünler',
        'siparis' => 'Siparişler',
        'kullanici' => 'Kullanıcılar',
    ];
@endphp

{{-- Checkbox seçilmemiş olsa bile formun gönderildiğini belirtir. --}}
<input type="hidden" name="izin_formu" value="1">

<div class="form-grup">
    <label for="label">Rol Adı</label>

    {{-- Hata sonrasında eski girdiyi, ilk açılışta mevcut rol adını gösterir. --}}
    <input
        type="text"
        id="label"
        name="label"
        value="{{ old('label', $formRolu?->label) }}"
        maxlength="255"
        required
        autofocus
    >

    <small class="form-aciklama">
        Örnek: Depo Sorumlusu, Admin, Satış Temsilcisi.
    </small>
</div>

<div class="form-grup">
    <label for="name">Rol Kodu</label>

    {{-- Sistemde kullanılan benzersiz rol kodunu gönderir. --}}
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $formRolu?->name) }}"
        maxlength="100"
        required
    >

    <small class="form-aciklama">
        Küçük İngilizce harfler, rakamlar ve aralarda tire kullanınız.
        Örnek: depo-sorumlusu.
    </small>
</div>

<div class="form-grup">
    <h2>Rol Yetkileri</h2>

    <p class="form-aciklama">
        Bu role bağlı kullanıcıların yapabileceği işlemleri seçiniz.
        Düzenleme sırasında kaldırılan seçimler rolden çıkarılır.
        Her işlem izni bağımsızdır; yalnızca işaretlediğiniz izinler role verilir.
    </p>

    {{-- Controller tarafından hazırlanan modül grupları üzerinde dolaşır. --}}
    @forelse ($izinGruplari as $modul => $izinler)
        <fieldset class="form-grup">
            <legend>
                {{-- Tanımlanmamış yeni modüllerde modül kodunu gösterir. --}}
                {{ $modulAdlari[$modul] ?? $modul }}
            </legend>

            {{-- Modüldeki her izni ayrı bir checkbox olarak gösterir. --}}
            @foreach ($izinler as $izin)
                <div>
                    <label for="izin-{{ $izin->id }}" class="izin-secimi">
                        <input
                            type="checkbox"
                            id="izin-{{ $izin->id }}"
                            name="permission_ids[]"
                            value="{{ $izin->id }}"
                            @checked(
                                in_array(
                                    (string) $izin->id,
                                    $seciliIzinler,
                                    true
                                )
                            )
                        >

                        {{-- Etiket yoksa izin kodunu gösterir. --}}
                        <span>{{ $izin->label ?? $izin->name }}</span>
                    </label>
                </div>
            @endforeach
        </fieldset>
    @empty
        <p class="uyari uyari-hata">
            Sistemde tanımlı izin bulunamadı.
        </p>
    @endforelse
</div>

<div class="form-butonlari">
    {{-- Oluşturma ve düzenleme ekranı buton metnini kendisi gönderecek. --}}
    <button type="submit" class="buton buton-birincil">
        {{ $butonMetni }}
    </button>

    <a href="{{ route('roller.index') }}" class="buton buton-ikincil">
        İptal
    </a>
</div>
