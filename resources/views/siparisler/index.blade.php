@extends('layouts.adminkit')

@section('title', __('panel.orders'))

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
            <h1 class="h3 mb-2">{{ __('panel.orders') }}</h1>
            <p class="text-muted mb-0">{{ __('panel.orders-description') }}</p>
        </div>
        @can('create', App\Models\Order::class)
            <a href="{{ route('siparisler.urun-sec') }}" class="btn btn-primary">{{ __('panel.create-order') }}</a>
        @endcan
    </section>

    <div class="card">
        <div class="card-body adminkit-tablo">
            <table id="siparisler-tablosu" class="table table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>{{ __('panel.order-number') }}</th>
                        <th>{{ __('panel.product') }}</th>
                        <th>{{ __('panel.customer') }}</th>
                        <th>{{ __('panel.quantity') }}</th>
                        <th>{{ __('panel.total-amount') }}</th>
                        <th>{{ __('panel.status') }}</th>
                        <th>{{ __('panel.date') }}</th>
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
            const paraBirimi = new Intl.NumberFormat({{ Js::from(app()->getLocale()) }}, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            new DataTable('#siparisler-tablosu', {
                layout: {
                    top1Start: {
                        buttons: [{
                                extend: 'copy',
                                text: {{ Js::from(__('panel.copy')) }},
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
                                text: {{ Js::from(__('panel.print-all')) }},
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4, 5, 6]
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
                                '<div class="text-muted small mt-1">' + guvenliMetin(satir.product_sku) +
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
                        className: 'text-nowrap',
                        render: function(veri, tur) {
                            return tur === 'display' ? paraBirimi.format(veri) + ' ₺' : veri;
                        }
                    },
                    {
                        data: 'status',
                        render: function(veri, tur) {
                            return tur === 'display' ?
                                '<span class="badge bg-warning text-dark">' + guvenliMetin(veri) +
                                '</span>' :
                                veri;
                        }
                    },
                    {
                        data: 'created_at',
                        className: 'text-nowrap',
                        render: function(veri, tur, satir) {
                            return tur === 'display' || tur === 'filter' ? satir
                                .created_at_display : veri;
                        }
                    }
                ],
                order: [],
                language: {
                    ...{{ Js::from(__('panel.datatable')) }},
                    zeroRecords: {{ Js::from(__('panel.no-matching-orders')) }},
                    emptyTable: {{ Js::from(__('panel.no-orders')) }}
                }
            });
        });
    </script>
@endpush
