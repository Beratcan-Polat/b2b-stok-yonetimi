@extends('layouts.app')

@section('title', 'Silinen Ürünler')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/3.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/colreorder/3.1.2/css/colReorder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/columncontrol/2.1.2/css/columnControl.dataTables.css">
@endpush

@section('content')
    <section class="baslik-alani">
        <div>
            <h1>Silinen Ürünler</h1>
            <p>Soft Delete ile silinen ürünleri görüntüleyin ve geri yükleyin.</p>
        </div>

        <a href="{{ route('urunler.index') }}" class="buton buton-ikincil">Ürünlere Dön</a>
    </section>

    <div class="tablo-kapsayici">
        <table id="silinen-urunler-tablosu">
            <thead>
                <tr>
                    <th>Görsel</th>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th>Stok</th>
                    <th>Silinme Tarihi</th>
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
                                '<div class="urun-alt-bilgi">SKU: ' + guvenliMetin(satir.sku) + '</div>';
                        }
                    },
                    { data: 'category_name', render: DataTable.render.text() },
                    {
                        data: 'stock',
                        render: function (veri, tur) {
                            return tur === 'display' ? veri + ' adet' : veri;
                        }
                    },
                    {
                        data: 'deleted_at',
                        render: function (veri, tur, satir) {
                            return tur === 'display' || tur === 'filter' ? satir.deleted_at_display : veri;
                        }
                    },
                    { data: 'actions', orderable: false, searchable: false }
                ],
                createdRow: function (satir) {
                    satir.classList.add('silinen-satir');
                },
                order: [],
                language: {
                    search: 'Ara:',
                    lengthMenu: 'Sayfada _MENU_ kayıt',
                    info: '_TOTAL_ kayıttan _START_–_END_ arası',
                    infoEmpty: 'Kayıt yok',
                    zeroRecords: 'Eşleşen silinmiş ürün bulunamadı.',
                    emptyTable: 'Silinen ürün bulunmuyor.',
                    paginate: { previous: 'Önceki', next: 'Sonraki' }
                }
            });
        });
    </script>
@endpush
