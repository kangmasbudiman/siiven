@extends('_layouts.app')

@section('title', 'Logistik - Pemasukan Barang')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('logistik.pemasukan.store') }}" method="post" id="form-pemasukan">
                    @csrf
                    <div class="card-header">
                        <h2 class="h6 font-weight-bold mb-0 card-title">
                            <i class="fas fa-arrow-down me-1"></i>
                            Pemasukan Barang ke Gudang Logistik
                            ({{ $ruangan->kode_ruangan }} - {{ $ruangan->nama_ruangan }})
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                        name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>

                                    @error('tanggal')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <input type="text" class="form-control @error('keterangan') is-invalid @enderror"
                                        name="keterangan" placeholder="cth: penerimaan dari pemasok, hibah, retur ruangan..."
                                        value="{{ old('keterangan') }}">

                                    @error('keterangan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle" id="tabel-item">
                                <thead>
                                    <tr>
                                        <th style="width:34%">Barang <span class="text-danger">*</span></th>
                                        <th class="text-center">Satuan</th>
                                        <th style="width:16%">Harga Satuan (Rp) <span class="text-danger">*</span></th>
                                        <th style="width:12%">Jumlah <span class="text-danger">*</span></th>
                                        <th class="text-end">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="4" class="text-end fw-bold">Grand Total</td>
                                        <td class="text-end fw-bold fs-6" id="grand-total">Rp 0</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @error('items')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        <button type="button" id="tambah-item" class="btn btn-sm btn-secondary">
                            <i class="fas fa-plus me-1"></i> Tambah Item
                        </button>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save me-1"></i> Simpan Pemasukan
                        </button>
                        <a class="btn btn-secondary" href="{{ route('logistik.index') }}">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="row-template">
        <tr class="item-row">
            <td>
                <input type="hidden" class="item-barang" value="">
                <button type="button" class="btn btn-light border text-start w-100 btn-cari-barang"
                    data-bs-toggle="modal" data-bs-target="#modalCariBarang">
                    <i class="fas fa-search me-1 text-muted"></i>
                    <span class="label-cari text-muted">Pilih barang...</span>
                </button>
            </td>
            <td class="item-satuan text-center text-muted">-</td>
            <td>
                <input type="number" class="form-control item-harga" min="0" step="1" placeholder="0" required>
            </td>
            <td>
                <input type="number" class="form-control item-jumlah" min="1" step="1" placeholder="0" required>
            </td>
            <td class="item-subtotal text-end fw-semibold">Rp 0</td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm btn-hapus" title="Hapus item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <div class="modal fade" id="modalCariBarang" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-search me-1"></i> Cari Barang
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="modal-cari" class="form-control form-control-lg mb-3"
                        placeholder="Ketik kode / nama barang..." autocomplete="off">
                    <div class="table-responsive" style="max-height: 55vh;">
                        <table class="table table-bordered table-striped align-middle mb-0" id="tabel-modal-barang">
                            <thead>
                                <tr>
                                    <th style="width:18%">Kode</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center" style="width:12%">Satuan</th>
                                    <th class="text-end" style="width:20%">Harga Terakhir</th>
                                    <th style="width:10%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($barangs as $barang)
                                    <tr data-id="{{ $barang->id }}"
                                        data-nama="{{ $barang->kode_barang }} - {{ $barang->nama_barang }}"
                                        data-satuan="{{ $barang->satuan }}"
                                        data-harga="{{ $hargaMap[$barang->id] ?? '' }}">
                                        <td class="td-kode">{{ $barang->kode_barang }}</td>
                                        <td class="fw-semibold">{{ $barang->nama_barang }}</td>
                                        <td class="text-center">{{ $barang->satuan }}</td>
                                        <td class="text-end">
                                            {{ isset($hargaMap[$barang->id]) ? 'Rp ' . number_format($hargaMap[$barang->id], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary btn-pilih">
                                                <i class="fas fa-check me-1"></i> Pilih
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data barang.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div id="modal-kosong" class="text-center text-muted py-4 d-none">
                        Tidak ada barang yang cocok dengan pencarian.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        #tabel-item { font-size: 1rem; }
        #tabel-item td { padding-top: .75rem; padding-bottom: .75rem; vertical-align: middle; }
        #tabel-item .form-control { font-size: 1rem; padding: .55rem .75rem; min-height: 44px; }
        #tabel-item .btn-cari-barang { min-height: 44px; padding: .55rem .75rem; font-size: 1rem; }
        #tabel-item .item-satuan { font-size: 1rem; }
        #tabel-item .item-subtotal { font-size: 1rem; white-space: nowrap; }
        #tabel-item #grand-total { font-size: 1.2rem; white-space: nowrap; }

        #modalCariBarang { font-size: 1rem; }
        #tabel-modal-barang td { padding: .7rem .75rem; vertical-align: middle; }
        #tabel-modal-barang .td-kode { white-space: nowrap; }
        #tabel-modal-barang tbody tr:hover { cursor: pointer; }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('form-pemasukan');
            var tbody = document.querySelector('#tabel-item tbody');
            var template = document.getElementById('row-template');
            var modalEl = document.getElementById('modalCariBarang');
            var cariInput = document.getElementById('modal-cari');
            var tabelModal = document.getElementById('tabel-modal-barang');
            var kosongEl = document.getElementById('modal-kosong');
            var rows = [];
            var activeRow = null;

            function formatRp(angka) {
                return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
            }

            function filterBarang() {
                var q = cariInput.value.toLowerCase();
                var visible = 0;
                tabelModal.querySelectorAll('tbody tr[data-id]').forEach(function (tr) {
                    var cocok = tr.dataset.nama.toLowerCase().indexOf(q) !== -1;
                    tr.classList.toggle('d-none', !cocok);
                    if (cocok) visible++;
                });
                kosongEl.classList.toggle('d-none', visible > 0);
            }

            function hitungRow(row) {
                var harga = parseInt(row.querySelector('.item-harga').value) || 0;
                var jumlah = parseInt(row.querySelector('.item-jumlah').value) || 0;
                var subtotal = harga * jumlah;
                row.querySelector('.item-subtotal').textContent = formatRp(subtotal);
                return subtotal;
            }

            function hitungGrandTotal() {
                var total = 0;
                rows.forEach(function (row) { total += hitungRow(row); });
                document.getElementById('grand-total').textContent = formatRp(total);
            }

            function reindex() {
                rows.forEach(function (row, i) {
                    row.querySelector('.item-barang').name = 'items[' + i + '][barang_id]';
                    row.querySelector('.item-harga').name = 'items[' + i + '][harga_satuan]';
                    row.querySelector('.item-jumlah').name = 'items[' + i + '][jumlah]';
                });
            }

            function pilihBarang(row, d) {
                var duplikat = rows.some(function (r) {
                    return r !== row && r.querySelector('.item-barang').value === d.id;
                });
                if (duplikat) {
                    alert('Barang tersebut sudah dipilih di item lain.');
                    return false;
                }

                row.querySelector('.item-barang').value = d.id;
                row.querySelector('.label-cari').innerHTML = '<strong>' + d.nama + '</strong>';
                row.querySelector('.item-satuan').textContent = d.satuan;
                if (d.harga && row.querySelector('.item-harga').value === '') {
                    row.querySelector('.item-harga').value = d.harga;
                }
                hitungGrandTotal();
                return true;
            }

            function tambahRow() {
                var row = template.content.firstElementChild.cloneNode(true);
                tbody.appendChild(row);
                rows.push(row);

                row.querySelector('.btn-cari-barang').addEventListener('click', function () {
                    activeRow = row;
                    cariInput.value = '';
                    filterBarang();
                });

                row.querySelector('.item-harga').addEventListener('input', hitungGrandTotal);
                row.querySelector('.item-jumlah').addEventListener('input', hitungGrandTotal);
                row.querySelector('.btn-hapus').addEventListener('click', function () {
                    row.remove();
                    rows = rows.filter(function (r) { return r !== row; });
                    if (rows.length === 0) tambahRow();
                    reindex();
                    hitungGrandTotal();
                });

                reindex();
            }

            tabelModal.addEventListener('click', function (e) {
                var btn = e.target.closest('.btn-pilih');
                var tr = e.target.closest('tr[data-id]');
                if (btn && tr && activeRow && pilihBarang(activeRow, tr.dataset)) {
                    bootstrap.Modal.getInstance(modalEl).hide();
                }
            });

            cariInput.addEventListener('input', filterBarang);
            modalEl.addEventListener('shown.bs.modal', function () { cariInput.focus(); });

            document.getElementById('tambah-item').addEventListener('click', tambahRow);

            form.addEventListener('submit', function (e) {
                var valid = rows.some(function (row) {
                    return row.querySelector('.item-barang').value !== '';
                });
                if (!valid) {
                    e.preventDefault();
                    alert('Minimal satu barang harus dipilih.');
                }
            });

            tambahRow();
        });
    </script>
@endpush
