@extends('layouts.adminkit')

@section('title', __('panel.categories-management'))

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
            <h1 class="h3 mb-2">{{ __('panel.categories-management') }}</h1>
            <p class="text-muted mb-0">{{ __('panel.categories-sentence') }}</p>
        </div>
        @can('create', App\Models\Category::class)
            <a href="{{ route('kategoriler.create') }}" class="btn btn-primary align-self-start">{{ __('panel.category-buton') }}</a>
        @endcan
    </section>

    <div class="card">
        <div class="card-body adminkit-tablo">
            <table id="kategoriler-tablosu" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('panel.category-name') }}</th>
                        <th>Slug</th>
                        <th>{{ __('panel.product-count') }}</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" ></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" ></script>
    <script src="https://cdn.datatables.net/buttons/4.1.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/4.1.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/4.1.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/4.1.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/4.1.1/js/dataTables.select.min.js"></script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const guvenliMetin = DataTable.render.text().display;

            new DataTable('#kategoriler-tablosu', {
                layout: {
                    top1Start: {
                        buttons: [
                            {
                                extend: 'copy',
                                text: {{ Js::from(__('panel.copy')) }},
                                exportOptions: { columns: [0, 1, 2, 3]}
                            },
                            {
                                extend: 'csv',
                                text: 'CSV',
                                exportOptions: { columns: [0, 1, 2, 3]}
                            },
                            {
                                extend: 'excel',
                                text: 'Excel',
                                exportOptions: { columns: [0, 1, 2, 3]}
                            },
                            {
                                extend: 'pdf',
                                text: 'PDF',
                                exportOptions: { columns: [0, 1, 2, 3]}
                            },
                            {
                                extend: 'print',
                                text: {{ Js::from(__('panel.print-all')) }},
                                exportOptions: {
                                    columns: [0, 1, 2, 3],
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
                ajax: '{{ route('kategoriler.veri') }}',
                columns: [{
                        data: 'id',
                        className: 'dt-left'
                    },
                    {
                        data: 'name',
                        render: DataTable.render.text()
                    },
                    {
                        data: 'slug',
                        render: function(veri, tur) {
                            if (tur === 'display') {
                                return '<span class="kod-metin">' + guvenliMetin(veri) + '</span>';
                            }

                            return veri;
                        }
                    },
                    {
                        data: 'products_count',
                        className: 'dt-left'
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
                    zeroRecords: {{ Js::from(__('panel.no-matching-categories')) }},
                    emptyTable: {{ Js::from(__('panel.no-categories')) }}
                }
            });
        });
    </script>
@endpush
