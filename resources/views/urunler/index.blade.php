@extends('layouts.adminkit')

@section('title', __('panel.products-management'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/3.1.2/css/colReorder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/columncontrol/2.1.2/css/columnControl.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/4.1.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/4.1.1/css/select.dataTables.min.css">
@endpush

@section('content')
    <section class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-2">{{ __('panel.products-management') }}</h1>
            <p class="text-muted mb-0">{{ __('panel.products-description') }}</p>
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2">
            @can('viewTrashed', App\Models\Product::class)
                <a href="{{ route('urunler.silinenler') }}" class="btn btn-secondary">{{ __('panel.deleted-products') }}</a>
            @endcan

            @can('create', App\Models\Product::class)
                <a href="{{ route('urunler.create') }}" class="btn btn-primary">{{ __('panel.new-product') }}</a>
            @endcan
        </div>
    </section>

    <div class="card">
        <div class="card-body">
            <div class="row align-items-end g-3">
                <div class="col-12 col-md-6 col-xl-4">
                    <label for="category_id" class="form-label">{{ __('panel.category') }}</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">{{ __('panel.all-categories') }}</option>
                        @foreach ($kategoriler as $kategori)
                            <option value="{{ $kategori->id }}" @selected(request('category_id') == $kategori->id)>
                                {{ $kategori->localizedName() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-auto">
                    <button type="button" id="urun-filtre-temizle" class="btn btn-secondary">{{ __('panel.clear') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body adminkit-tablo">
            <table id="urunler-tablosu" class="table table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>{{ __('panel.image') }}</th>
                        <th>{{ __('panel.product') }}</th>
                        <th>{{ __('panel.category') }}</th>
                        <th>{{ __('panel.price') }}</th>
                        <th>{{ __('panel.stock') }}</th>
                        <th>{{ __('panel.actions') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
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
            const paraBirimi = new Intl.NumberFormat({{ Js::from(app()->getLocale()) }}, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            const tablo = new DataTable('#urunler-tablosu', {
                layout: {
                    top1Start: {
                        buttons: [{
                                extend: 'copy',
                                text: {{ Js::from(__('panel.copy')) }},
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
                                text: {{ Js::from(__('panel.print-all')) }},
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'selectNone',
                                text: {{ Js::from(__('panel.clear-selection')) }}
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
                                '<div class="text-muted small mt-1">SKU: ' + guvenliMetin(satir.sku) +
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
                        className: 'text-nowrap',
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
                                '<span class="badge bg-success">' + veri + ' ' + {{ Js::from(__('panel.units')) }} + '</span>' :
                                '<span class="badge bg-danger">' + {{ Js::from(__('panel.out-of-stock')) }} + '</span>';
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
                    ...{{ Js::from(__('panel.datatable')) }},
                    zeroRecords: {{ Js::from(__('panel.no-matching-products')) }},
                    emptyTable: {{ Js::from(__('panel.no-products')) }}
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
