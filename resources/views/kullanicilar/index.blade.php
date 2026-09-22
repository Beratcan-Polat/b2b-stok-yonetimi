@extends('layouts.app')

@section('title', 'Kullanıcı Yönetimi')

@section('content')

    <section class="baslik-alani">
        <div>
            <h1>Kullanıcı Yönetimi</h1>
            <p>Panel kullanıcılarını ekleyin, rollerini belirleyin ve yönetin.</p>
        </div>

        @can('create', App\Models\User::class)
            <a href="{{ route('kullanicilar.create') }}" class="buton buton-birincil">
                Yeni Kullanıcı Ekle
            </a>
        @endcan
    </section>

    <div class="tablo-kapsayici">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad Soyad</th>
                    <th>E-posta</th>
                    <th>Rol</th>
                    <th>İşlemler</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($kullanicilar as $kullanici)
                    <tr>
                        <td>{{ $kullanici->id }}</td>

                        <td>
                            <strong>{{ $kullanici->name }}</strong>

                            @if ($kullanici->id === auth()->id())
                                <div class="urun-alt-bilgi">Bu hesap sizsiniz</div>
                            @endif
                        </td>

                        <td>{{ $kullanici->email }}</td>

                        <td>
                            <span class="kod-metin">
                                {{ $kullanici->roles->first()?->label ?? 'Rol atanmamış' }}
                            </span>
                        </td>

                        <td>
                            <div class="islem-alani">
                                @can('update', $kullanici)
                                    <a
                                        href="{{ route('kullanicilar.edit', $kullanici) }}"
                                        class="buton buton-ikincil buton-kucuk"
                                    >
                                        Düzenle
                                    </a>
                                @endcan

                                @can('delete', $kullanici)
                                    @if ($kullanici->id !== auth()->id())
                                        <form
                                            action="{{ route('kullanicilar.destroy', $kullanici) }}"
                                            method="POST"
                                            class="satir-ici-form"
                                            onsubmit="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="buton buton-tehlike buton-kucuk">
                                                Sil
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="bos-kayit">
                            Henüz kullanıcı bulunmuyor.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
