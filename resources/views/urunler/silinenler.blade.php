@extends('layouts.adminkit')

@section('title', __('panel.deleted-products'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/3.1.2/css/colReorder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/columncontrol/2.1.2/css/columnControl.dataTables.css">
@endpush

@section('content')
    <section class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-2">{{ __('panel.deleted-products') }}</h1>
            <p class="text-muted mb-0">{{ __('panel.deleted-products-description') }}</p>
        </div>

        @can('viewAny', App\Models\Product::class)
            <a href="{{ route('urunler.index') }}" class="btn btn-secondary">{{ __('panel.back-products') }}</a>
        @else
            <a href="{{ route('anasayfa') }}" class="btn btn-secondary">{{ __('panel.home') }}</a>
        @endcan
    </section>

    <div class="card">
        <div class="card-body adminkit-tablo">
            <table id="silinen-urunler-tablosu" class="table table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>{{ __('panel.image') }}</th>
                        <th>{{ __('panel.product') }}</th>
                        <th>{{ __('panel.category') }}</th>
                        <th>{{ __('panel.stock') }}</th>
                        <th>{{ __('panel.deleted-at') }}</th>
                        <th>{{ __('panel.action') }}</th>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const guvenliMetin = DataTable.render.text().display;

            new DataTable('#silinen-urunler-tablosu', {
                columnControl: ['order', 'reorder'],
                stateSave: true,
                stateDuration: 0,
                ordering: { indicators: false, handler: false },
                ajax: '{{ route('urunler.silinenler.veri') }}',
                columns: [
                    { data: 'image', orderable: false, searchable: false },
                    {
                        data: 'name',
                        render: function (veri, tur, satir) {
                            if (tur === 'filter') {
                                return veri + ' ' + satir.sku;
                            }

                            if (tur !== 'display') {
                                return veri;
                            }

                            return '<strong>' + guvenliMetin(veri) + '</strong>' +
                                '<div class="text-muted small mt-1">SKU: ' + guvenliMetin(satir.sku) + '</div>';
                        }
                    },
                    { data: 'category_name', render: DataTable.render.text() },
                    {
                        data: 'stock',
                        render: function (veri, tur) {
                            return tur === 'display' ? veri + ' ' + {{ Js::from(__('panel.units')) }} : veri;
                        }
                    },
                    {
                        data: 'deleted_at',
                        className: 'text-nowrap',
                        render: function (veri, tur, satir) {
                            return tur === 'display' || tur === 'filter' ? satir.deleted_at_display : veri;
                        }
                    },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                createdRow: function (satir) {
                    satir.classList.add('text-muted');
                },
                order: [],
                language: {
                    ...{{ Js::from(__('panel.datatable')) }},
                    zeroRecords: {{ Js::from(__('panel.no-matching-deleted-products')) }},
                    emptyTable: {{ Js::from(__('panel.no-deleted-products')) }}
                }
            });
        });
    </script>
@endpush
