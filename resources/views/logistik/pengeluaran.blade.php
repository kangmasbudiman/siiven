@extends('_layouts.app')

@section('title', 'Logistik - Pengeluaran Barang')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('logistik.pengeluaran.store') }}" method="post" id="form-pengeluaran">
                    @csrf
                    <div class="card-header">
                        <h2 class="h6 font-weight-bold mb-0 card-title">
                            <i class="fas fa-arrow-up me-1"></i>
                            Pengeluaran Barang dari Gudang Logistik
                            ({{ $ruangan->kode_ruangan }} - {{ $ruangan->nama_ruangan }})
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Ruangan Tujuan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('ruangan_tujuan_id') is-invalid @enderror"
                                        name="ruangan_tujuan_id" required>
                                        <option value="">-- Pilih Ruangan --</option>
                                        @foreach ($ruangans as $tujuan)
                                            <option value="{{ $tujuan->id }}" {{ old('ruangan_tujuan_id') == $tujuan->id ? 'selected' : '' }}>
                                                {{ $tujuan->kode_ruangan }} - {{ $tujuan->nama_ruangan }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('ruangan_tujuan_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="text-muted">Stok ruangan tujuan otomatis bertambah.</small>
                                </div>
                            </div>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <input type="text" class="form-control @error('keterangan') is-invalid @enderror"
                                        name="keterangan" placeholder="cth: distribusi pemakaian ruangan..."
                                        value="{{ old('keterangan') }}">

                                    @error('keterangan')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if ($stoks->isEmpty())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Belum ada stok barang di gudang logistik. Catat pemasukan terlebih dahulu.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle" id="tabel-item">
                                    <thead>
                                        <tr>
                                            <th style="width:32%">Barang <span class="text-danger">*</span></th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Stok Tersedia</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th style="width:12%">Jumlah <span class="text-danger">*</span></th>
                                            <th class="text-end">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr class="table-light">
                                            <td colspan="5" class="text-end fw-bold">Grand Total</td>
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
                        @endif
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-warning" type="submit" {{ $stoks->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-save me-1"></i> Simpan Pengeluaran
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
            <td class="item-stok text-center">-</td>
            <td class="item-harga text-end">-</td>
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
                                    <th class="text-center" style="width:14%">Stok</th>
                                    <th class="text-end" style="width:18%">Harga Satuan</th>
                                    <th style="width:10%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stoks as $stok)
                                    <tr data-id="{{ $stok->barang_id }}"
                                        data-nama="{{ $stok->barang->kode_barang }} - {{ $stok->barang->nama_barang }}"
                                        data-satuan="{{ $stok->barang->satuan }}"
                                        data-stok="{{ $stok->jumlah }}"
                                        data-harga="{{ $stok->harga ?? 0 }}">
                                        <td class="td-kode">{{ $stok->barang->kode_barang }}</td>
                                        <td class="fw-semibold">{{ $stok->barang->nama_barang }}</td>
                                        <td class="text-center">{{ $stok->barang->satuan }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $stok->jumlah > 0 ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $stok->jumlah }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            {{ $stok->harga ? 'Rp ' . number_format($stok->harga, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary btn-pilih">
                                                <i class="fas fa-check me-1"></i> Pilih
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada stok barang di gudang.</td></tr>
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
        #tabel-item .item-satuan, #tabel-item .item-stok, #tabel-item .item-harga { font-size: 1rem; }
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
            var form = document.getElementById('form-pengeluaran');
            var tbody = document.querySelector('#tabel-item tbody');
            var template = document.getElementById('row-template');
            var modalEl = document.getElementById('modalCariBarang');
            var cariInput = document.getElementById('modal-cari');
            var tabelModal = document.getElementById('tabel-modal-barang');
            var kosongEl = document.getElementById('modal-kosong');
            var rows = [];
            var activeRow = null;

            if (!tbody) return;

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
                var jumlah = parseInt(row.querySelector('.item-jumlah').value) || 0;
                var harga = parseInt(row.querySelector('.item-barang').value ? row.dataset.harga : 0) || 0;
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
                row.dataset.harga = d.harga;
                row.querySelector('.label-cari').innerHTML = '<strong>' + d.nama + '</strong>';
                row.querySelector('.item-satuan').textContent = d.satuan;
                row.querySelector('.item-stok').textContent = d.stok + ' ' + d.satuan;
                row.querySelector('.item-harga').textContent = formatRp(d.harga);
                row.querySelector('.item-jumlah').max = d.stok;
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
