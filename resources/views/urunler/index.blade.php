@extends('layouts.app')

@section('title', 'Ürün Yönetimi')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/3.1.2/css/colReorder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/columncontrol/2.1.2/css/columnControl.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/4.1.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/4.1.1/css/select.dataTables.min.css">
@endpush

@section('content')
    <section class="baslik-alani">
        <div>
            <h1>Ürün Yönetimi</h1>
            <p>Ürün bilgilerini, görsellerini, fiyatlarını ve stoklarını yönetin.</p>
        </div>

        <div class="baslik-butonlari">
            @can('viewTrashed', App\Models\Product::class)
                <a href="{{ route('urunler.silinenler') }}" class="buton buton-ikincil">Silinen Ürünler</a>
            @endcan

            @can('create', App\Models\Product::class)
                <a href="{{ route('urunler.create') }}" class="buton buton-birincil">Yeni Ürün Ekle</a>
            @endcan
        </div>
    </section>

    <div class="filtre-formu">
        <div class="filtre-alanlari">
            <div class="form-grup filtre-grup">
                <label for="category_id">Kategori</label>
                <select id="category_id" name="category_id">
                    <option value="">Tüm kategoriler</option>
                    @foreach ($kategoriler as $kategori)
                        <option value="{{ $kategori->id }}" @selected(request('category_id') == $kategori->id)>
                            {{ $kategori->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filtre-butonlari">
                <button type="button" id="urun-filtre-temizle" class="buton buton-ikincil">Temizle</button>
            </div>
        </div>
    </div>

    <div class="tablo-kapsayici">
        <table id="urunler-tablosu">
            <thead>
                <tr>
                    <th>Görsel</th>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th>Fiyat</th>
                    <th>Stok</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/3.1.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/colreorder/3.1.2/js/dataTables.colReorder.js"></script>
    <script src="https://cdn.datatables.net/columncontrol/2.1.2/js/dataTables.columnControl.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/4.1.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/4.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/4.1.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/4.1.1/js/dataTables.select.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const guvenliMetin = DataTable.render.text().display;
            const kategoriAlani = document.getElementById('category_id');
            const paraBirimi = new Intl.NumberFormat('tr-TR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            const tablo = new DataTable('#urunler-tablosu', {
                layout: {
                    top1Start: {
                        buttons: [{
                                extend: 'copy',
                                text: 'Kopyala',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'csv',
                                text: 'CSV',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'excel',
                                text: 'Excel',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: 'PDF',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'print',
                                text: 'Tümünü Yazdır',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'selectNone',
                                text: 'Seçimi Temizle'
                            }
                        ]
                    }
                },
                select: {
                    selector: 'td:not(.dt-no-select)'
                },
                columnControl: ['order', 'reorder'],
                stateSave: true,
                stateDuration: 0,
                ordering: {
                    indicators: false,
                    handler: false
                },
                ajax: '{{ route('urunler.veri') }}',
                columns: [{
                        data: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        render: function(veri, tur, satir) {
                            if (tur === 'filter') {
                                return veri + ' ' + satir.sku;
                            }

                            if (tur !== 'display') {
                                return veri;
                            }

                            return '<strong>' + guvenliMetin(veri) + '</strong>' +
                                '<div class="urun-alt-bilgi">SKU: ' + guvenliMetin(satir.sku) +
                                '</div>';
                        }
                    },
                    {
                        data: 'category_name',
                        name: 'category',
                        render: DataTable.render.text()
                    },
                    {
                        data: 'price',
                        render: function(veri, tur) {
                            return tur === 'display' ? paraBirimi.format(veri) + ' ₺' : veri;
                        }
                    },
                    {
                        data: 'stock',
                        render: function(veri, tur) {
                            if (tur !== 'display') {
                                return veri;
                            }

                            return veri > 0 ?
                                '<span class="stok-etiketi stok-var">' + veri + ' adet</span>' :
                                '<span class="stok-etiketi stok-yok">Stokta Yok</span>';
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'dt-no-select'
                    }
                ],
                order: [],
                initComplete: function() {
                    const kategoriAdi = kategoriAlani.value ? kategoriAlani.selectedOptions[0].text :
                    '';
                    const kategoriSutunu = this.api().column('category:name');

                    if (kategoriSutunu.search() !== kategoriAdi) {
                        kategoriSutunu.search(kategoriAdi, {
                            exact: true
                        }).draw();
                    }
                },
                language: {
                    search: 'Ara:',
                    lengthMenu: 'Sayfada _MENU_ kayıt',
                    info: '_TOTAL_ kayıttan _START_–_END_ arası',
                    infoEmpty: 'Kayıt yok',
                    zeroRecords: 'Eşleşen ürün bulunamadı.',
                    emptyTable: 'Henüz ürün bulunmuyor.',
                    paginate: {
                        previous: 'Önceki',
                        next: 'Sonraki'
                    }
                }
            });

            function adresiGuncelle() {
                const adres = new URL(window.location.href);
                adres.searchParams.delete('search');

                if (kategoriAlani.value) {
                    adres.searchParams.set('category_id', kategoriAlani.value);
                } else {
                    adres.searchParams.delete('category_id');
                }

                window.history.replaceState(null, '', adres.pathname + adres.search);
            }

            kategoriAlani.addEventListener('change', function() {
                adresiGuncelle();
                const kategoriAdi = kategoriAlani.value ? kategoriAlani.selectedOptions[0].text : '';
                tablo.column('category:name').search(kategoriAdi, {
                    exact: true
                }).draw();
            });

            document.getElementById('urun-filtre-temizle').addEventListener('click', function() {
                kategoriAlani.value = '';
                adresiGuncelle();
                tablo.column('category:name').search('');
                tablo.search('').draw();
            });
        });
    </script>
@endpush
