@extends('layouts.adminkit')

@section('title', __('panel.roles'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/3.1.2/css/colReorder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/columncontrol/2.1.2/css/columnControl.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/4.1.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/4.1.1/css/select.dataTables.min.css">
@endpush

@section('content')
    <section class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-2">{{ __('panel.roles') }}</h1>
            <p class="text-muted mb-0">{{ __('panel.roles-description') }}</p>
        </div>

        @can('create', App\Models\Role::class)
            <a href="{{ route('roller.create') }}" class="btn btn-primary align-self-start">{{ __('panel.new-role') }}</a>
        @endcan
    </section>

    <div class="card">
        <div class="card-body adminkit-tablo">
            <table id="roller-tablosu" class="table table-hower w-100">
                <thead>
                    <tr>
                        <th>{{ __('panel.role-name') }}</th>
                        <th>{{ __('panel.role-code') }}</th>
                        <th>{{ __('panel.user-count') }}</th>
                        <th>{{ __('panel.permission-count') }}</th>
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

            new DataTable('#roller-tablosu', {
                layout: {
                    top1Start: {
                        buttons: [{
                                extend: 'copy',
                                text: {{ Js::from(__('panel.copy')) }},
                                exportOptions: {
                                    columns: [0, 1, 2, 3]
                                }
                            },
                            {
                                extend: 'csv',
                                text: 'CSV',
                                exportOptions: {
                                    columns: [0, 1, 2, 3]
                                }
                            },
                            {
                                extend: 'excel',
                                text: 'Excel',
                                exportOptions: {
                                    columns: [0, 1, 2, 3]
                                }
                            },
                            {
                                extend: 'pdf',
                                text: 'PDF',
                                exportOptions: {
                                    columns: [0, 1, 2, 3]
                                }
                            },
                            {
                                extend: 'print',
                                text: {{ Js::from(__('panel.print-all')) }},
                                exportOptions: {
                                    columns: [0, 1, 2, 3]
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
                ajax: '{{ route('roller.veri') }}',
                columns: [{
                        data: 'label',
                        render: function(veri, tur, satir) {
                            if (tur !== 'display') {
                                return veri;
                            }

                            const aciklama = satir.is_super_admin ?
                                '<div class="urun-alt-bilgi">' + {{ Js::from(__('panel.protected-role')) }} + '</div>' :
                                '';

                            return '<strong>' + guvenliMetin(veri) + '</strong>' + aciklama;
                        }
                    },
                    {
                        data: 'name',
                        render: function(veri, tur) {
                            return tur === 'display' ?
                                '<span class="kod-metin">' + guvenliMetin(veri) + '</span>' :
                                veri;
                        }
                    },
                    {
                        data: 'users_count'
                    },
                    {
                        data: 'permissions_count',
                        render: function(veri, tur, satir) {
                            return (tur === 'display' || tur === 'filter') && satir.is_super_admin ?
                                {{ Js::from(__('panel.all-permissions')) }} :
                                veri;
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
                language: {
                    ...{{ Js::from(__('panel.datatable')) }},
                    zeroRecords: {{ Js::from(__('panel.no-matching-roles')) }},
                    emptyTable: {{ Js::from(__('panel.no-roles')) }}
                }
            });
        });
    </script>
@endpush
