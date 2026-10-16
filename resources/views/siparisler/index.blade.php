@extends('layouts.app')

@section('title', 'Siparişler')

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
            <h1>Siparişler</h1>
            <p>Oluşturulan siparişleri ve toplam tutarlarını görüntüleyin.</p>
        </div>
    </section>

    <div class="tablo-kapsayici">
        <table id="siparisler-tablosu">
            <thead>
                <tr>
                    <th>Sipariş No</th>
                    <th>Ürün</th>
                    <th>Müşteri</th>
                    <th>Adet</th>
                    <th>Toplam Tutar</th>
                    <th>Durum</th>
                    <th>Tarih</th>
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
            const paraBirimi = new Intl.NumberFormat('tr-TR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            new DataTable('#siparisler-tablosu', {
                layout: {
                    top1Start: {
                        buttons: [{
                                extend: 'copy',
                                text: 'Kopyala',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'csv',
                                text: 'CSV',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'excel',
                                text: 'Excel',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: 'PDF',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
                                }
                            },
                            {
                                extend: 'print',
                                text: 'Tümünü Yazdır',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
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
                ajax: '{{ route('siparisler.veri') }}',
                columns: [{
                        data: 'id',
                        render: function(veri, tur) {
                            return tur === 'display' ? '#' + veri : veri;
                        }
                    },
                    {
                        data: 'product_name',
                        render: function(veri, tur, satir) {
                            if (tur === 'filter') {
                                return veri + ' ' + satir.product_sku;
                            }

                            if (tur !== 'display') {
                                return veri;
                            }

                            return '<strong>' + guvenliMetin(veri) + '</strong>' +
                                '<div class="urun-alt-bilgi">' + guvenliMetin(satir.product_sku) +
                                '</div>';
                        }
                    },
                    {
                        data: 'customer_name',
                        render: DataTable.render.text()
                    },
                    {
                        data: 'quantity'
                    },
                    {
                        data: 'total_price',
                        className: 'tutar',
                        render: function(veri, tur) {
                            return tur === 'display' ? paraBirimi.format(veri) + ' ₺' : veri;
                        }
                    },
                    {
                        data: 'status',
                        render: function(veri, tur) {
                            return tur === 'display' ?
                                '<span class="durum-etiketi durum-bekliyor">' + guvenliMetin(veri) +
                                '</span>' :
                                veri;
                        }
                    },
                    {
                        data: 'created_at',
                        render: function(veri, tur, satir) {
                            return tur === 'display' || tur === 'filter' ? satir
                                .created_at_display : veri;
                        }
                    }
                ],
                order: [],
                language: {
                    search: 'Ara:',
                    lengthMenu: 'Sayfada _MENU_ kayıt',
                    info: '_TOTAL_ kayıttan _START_–_END_ arası',
                    infoEmpty: 'Kayıt yok',
                    zeroRecords: 'Eşleşen sipariş bulunamadı.',
                    emptyTable: 'Henüz sipariş oluşturulmadı.',
                    paginate: {
                        previous: 'Önceki',
                        next: 'Sonraki'
                    }
                }
            });
        });
    </script>
@endpush
