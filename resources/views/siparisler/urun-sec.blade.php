@extends('layouts.adminkit')

@section('title', __('panel.select-order-product'))

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/3.1.2/css/colReorder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/columncontrol/2.1.2/css/columnControl.dataTables.css">
@endpush

@section('content')
    <section class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 mb-2">{{ __('panel.select-order-product') }}</h1>
            <p class="text-muted mb-0">{{ __('panel.select-order-product-description') }}</p>
        </div>
        @can('viewAny', App\Models\Order::class)
            <a href="{{ route('siparisler.index') }}" class="btn btn-secondary">{{ __('panel.orders') }}</a>
        @endcan
    </section>

    <div class="card">
        <div class="card-body adminkit-tablo">
            <table id="siparis-urun-sec-tablosu" class="table table-striped align-middle w-100">
                <thead>
                    <tr>
                        <th>{{ __('panel.product') }}</th>
                        <th>SKU</th>
                        <th>{{ __('panel.stock') }}</th>
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
            new DataTable('#siparis-urun-sec-tablosu', {
                columnControl: ['order', 'reorder'],
                stateSave: true,
                stateDuration: 0,
                ordering: { indicators: false, handler: false },
                ajax: '{{ route('siparisler.urun-sec.veri') }}',
                columns: [
                    { data: 'name', render: DataTable.render.text() },
                    { data: 'sku', render: DataTable.render.text() },
                    { data: 'stock' },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                order: [],
                language: {
                    ...{{ Js::from(__('panel.datatable')) }},
                    zeroRecords: {{ Js::from(__('panel.no-matching-products')) }},
                    emptyTable: {{ Js::from(__('panel.no-orderable-products')) }}
                }
            });
        });
    </script>
@endpush
