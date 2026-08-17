@extends('_layouts.app')

@section('title', 'Logistik - Riwayat Transaksi')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>
                            Riwayat Transaksi Gudang Logistik
                            <small class="text-muted">({{ $ruangan->kode_ruangan }} - {{ $ruangan->nama_ruangan }})</small>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('logistik.riwayat') }}" method="get" class="row g-2 align-items-end mb-3">
                            <div class="col-md-2">
                                <label class="form-label mb-1">Tipe</label>
                                <select name="tipe" class="form-control form-control-sm">
                                    <option value="">Semua</option>
                                    <option value="masuk" {{ request('tipe') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                                    <option value="keluar" {{ request('tipe') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label mb-1">Barang</label>
                                <select name="barang_id" class="form-control form-control-sm">
                                    <option value="">Semua Barang</option>
                                    @foreach ($barangs as $barang)
                                        <option value="{{ $barang->id }}" {{ request('barang_id') == $barang->id ? 'selected' : '' }}>
                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1">Dari Tanggal</label>
                                <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-1">Sampai Tanggal</label>
                                <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                                <a href="{{ route('logistik.riwayat') }}" class="btn btn-secondary btn-sm">Reset</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kode Transaksi</th>
                                        <th class="text-center">Tipe</th>
                                        <th class="text-center">Jumlah Item</th>
                                        <th class="text-center">Total Qty</th>
                                        <th class="text-end">Total Nilai</th>
                                        <th>Dari &rarr; Ke</th>
                                        <th>Dicatat Oleh</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transaksis as $t)
                                        <tr>
                                            <td>{{ ($transaksis->currentPage() - 1) * $transaksis->perPage() + $loop->iteration }}</td>
                                            <td>{{ $t->tanggal->format('d-m-Y') }}</td>
                                            <td><code>{{ $t->kode }}</code></td>
                                            <td class="text-center">
                                                @if ($t->tipe == 'masuk')
                                                    <span class="badge bg-success">Masuk</span>
                                                @else
                                                    <span class="badge bg-danger">Keluar</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $t->jumlah_item }}</td>
                                            <td class="text-center">{{ $t->total_qty }}</td>
                                            <td class="text-end">Rp {{ number_format($t->total_nilai, 0, ',', '.') }}</td>
                                            <td><small>{{ $t->dari }} &rarr; {{ $t->ke }}</small></td>
                                            <td>{{ $t->user }}</td>
                                            <td>{{ $t->keterangan ?? '-' }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-info btn-sm btn-detail" data-kode="{{ $t->kode }}">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted py-4">
                                                Tidak ada transaksi.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $transaksis->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-invoice me-1"></i> Detail Transaksi
                        <code id="detail-kode"></code>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="detail-meta" class="mb-3"></div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="detail-items"></tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="5" class="text-end fw-bold">Total</td>
                                    <td class="text-end fw-bold" id="detail-total">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('modalDetail');
            var detailModal = new bootstrap.Modal(modalEl);
            var baseUrl = '{{ url('logistik/riwayat/detail') }}' + '/';

            function formatRp(angka) {
                return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
            }

            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.btn-detail');
                if (!btn) return;

                var kode = btn.dataset.kode;
                document.getElementById('detail-kode').textContent = kode;
                document.getElementById('detail-meta').innerHTML = '';
                document.getElementById('detail-items').innerHTML =
                    '<tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>';
                document.getElementById('detail-total').textContent = 'Rp 0';
                detailModal.show();

                fetch(baseUrl + encodeURIComponent(kode))
                    .then(function (res) { return res.json(); })
                    .then(function (d) {
                        var tipe = d.tipe === 'masuk'
                            ? '<span class="badge bg-success">Masuk</span>'
                            : '<span class="badge bg-danger">Keluar</span>';
                        var meta = tipe + ' &nbsp; Tanggal: <strong>' + d.tanggal + '</strong>';
                        if (d.keterangan) meta += ' &nbsp; Keterangan: ' + d.keterangan;
                        document.getElementById('detail-meta').innerHTML = meta;

                        var rows = '';
                        var total = 0;
                        d.items.forEach(function (it) {
                            total += Number(it.subtotal);
                            rows += '<tr>'
                                + '<td>' + it.kode_barang + '</td>'
                                + '<td>' + it.nama_barang + '</td>'
                                + '<td class="text-center">' + it.satuan + '</td>'
                                + '<td class="text-center">' + it.jumlah + '</td>'
                                + '<td class="text-end">' + (it.harga_satuan ? formatRp(it.harga_satuan) : '-') + '</td>'
                                + '<td class="text-end">' + formatRp(it.subtotal) + '</td>'
                                + '</tr>';
                        });
                        document.getElementById('detail-items').innerHTML = rows ||
                            '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada item.</td></tr>';
                        document.getElementById('detail-total').textContent = formatRp(total);
                    })
                    .catch(function () {
                        document.getElementById('detail-items').innerHTML =
                            '<tr><td colspan="6" class="text-center text-danger py-4">Gagal memuat detail.</td></tr>';
                    });
            });
        });
    </script>
@endpush
