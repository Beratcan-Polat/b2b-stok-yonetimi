@extends('layouts.app')

@section('title', 'Sipariş İçin Ürün Seç')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/3.1.2/css/colReorder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/columncontrol/2.1.2/css/columnControl.dataTables.css">
@endpush

@section('content')
    <section class="baslik-alani">
        <div>
            <h1>Sipariş İçin Ürün Seç</h1>
            <p>Sipariş oluşturmak istediğiniz stoktaki ürünü seçin.</p>
        </div>
    </section>

    <div class="tablo-kapsayici">
        <table id="siparis-urun-sec-tablosu">
            <thead>
                <tr>
                    <th>Ürün</th>
                    <th>SKU</th>
                    <th>Stok</th>
                    <th>İşlem</th>
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
                    search: 'Ara:',
                    lengthMenu: 'Sayfada _MENU_ kayıt',
                    info: '_TOTAL_ kayıttan _START_–_END_ arası',
                    infoEmpty: 'Kayıt yok',
                    zeroRecords: 'Eşleşen ürün bulunamadı.',
                    emptyTable: 'Sipariş verilebilecek ürün bulunmuyor.',
                    paginate: { previous: 'Önceki', next: 'Sonraki' }
                }
            });
        });
    </script>
@endpush
