@extends('_layouts.app')

@section('title', 'Logistik - Laporan Pemasukan & Pengeluaran')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Laporan Pemasukan &amp; Pengeluaran
                            <small class="text-muted">({{ $ruangan->kode_ruangan }} - {{ $ruangan->nama_ruangan }})</small>
                        </h4>
                        <div class="btn-group">
                            <a href="{{ route('logistik.laporan.pdf', request()->query()) }}" target="_blank"
                                class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </a>
                            <a href="{{ route('logistik.laporan.excel', request()->query()) }}"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i> Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('logistik.laporan') }}" method="get" class="row g-2 align-items-end mb-3">
                            <div class="col-md-2">
                                <label class="form-label mb-1">Periode</label>
                                <select name="periode" id="sel-periode" class="form-control form-control-sm">
                                    <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                                    <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                </select>
                            </div>
                            <div class="col-md-2 {{ $periode == 'bulanan' ? 'd-none' : '' }}" id="wrap-tanggal">
                                <label class="form-label mb-1">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}">
                            </div>
                            <div class="col-md-2 {{ $periode == 'harian' ? 'd-none' : '' }}" id="wrap-bulan">
                                <label class="form-label mb-1">Bulan</label>
                                <select name="bulan" class="form-control form-control-sm">
                                    @foreach ([1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $num => $nama)
                                        <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 {{ $periode == 'harian' ? 'd-none' : '' }}" id="wrap-tahun">
                                <label class="form-label mb-1">Tahun</label>
                                <input type="number" name="tahun" min="2000" max="2100" class="form-control form-control-sm" value="{{ $tahun }}">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter me-1"></i> Tampilkan
                                </button>
                                <a href="{{ route('logistik.laporan') }}" class="btn btn-secondary btn-sm">Reset</a>
                            </div>
                        </form>

                        <h6 class="text-muted">{{ $label }}</h6>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body py-3">
                                        <div class="small text-uppercase">Total Nilai Pemasukan</div>
                                        <div class="fs-4 fw-bold">Rp {{ number_format($nilaiMasuk, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-danger text-white">
                                    <div class="card-body py-3">
                                        <div class="small text-uppercase">Total Nilai Pengeluaran</div>
                                        <div class="fs-4 fw-bold">Rp {{ number_format($nilaiKeluar, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body py-3">
                                        <div class="small text-uppercase">Jumlah Transaksi</div>
                                        <div class="fs-4 fw-bold">{{ $jumlahTransaksi }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-2"><i class="fas fa-arrow-down text-success me-1"></i> Transaksi Masuk</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kode Transaksi</th>
                                        <th class="text-center">Item</th>
                                        <th class="text-center">Total Qty</th>
                                        <th class="text-end">Total Nilai</th>
                                        <th>Dari &rarr; Ke</th>
                                        <th>Dicatat Oleh</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $masuk = $transaksis->where('tipe', 'masuk'); @endphp
                                    @forelse ($masuk as $t)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $t->tanggal->format('d-m-Y') }}</td>
                                            <td><code>{{ $t->kode }}</code></td>
                                            <td class="text-center">{{ $t->jumlah_item }}</td>
                                            <td class="text-center">{{ $t->total_qty }}</td>
                                            <td class="text-end">Rp {{ number_format($t->total_nilai, 0, ',', '.') }}</td>
                                            <td><small>{{ $t->dari }} &rarr; {{ $t->ke }}</small></td>
                                            <td>{{ $t->user }}</td>
                                            <td>{{ $t->keterangan ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center text-muted py-3">Tidak ada transaksi masuk pada periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <h5 class="mb-2"><i class="fas fa-arrow-up text-danger me-1"></i> Transaksi Keluar</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kode Transaksi</th>
                                        <th class="text-center">Item</th>
                                        <th class="text-center">Total Qty</th>
                                        <th class="text-end">Total Nilai</th>
                                        <th>Dari &rarr; Ke</th>
                                        <th>Dicatat Oleh</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $keluar = $transaksis->where('tipe', 'keluar'); @endphp
                                    @forelse ($keluar as $t)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $t->tanggal->format('d-m-Y') }}</td>
                                            <td><code>{{ $t->kode }}</code></td>
                                            <td class="text-center">{{ $t->jumlah_item }}</td>
                                            <td class="text-center">{{ $t->total_qty }}</td>
                                            <td class="text-end">Rp {{ number_format($t->total_nilai, 0, ',', '.') }}</td>
                                            <td><small>{{ $t->dari }} &rarr; {{ $t->ke }}</small></td>
                                            <td>{{ $t->user }}</td>
                                            <td>{{ $t->keterangan ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center text-muted py-3">Tidak ada transaksi keluar pada periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <h5 class="mb-2"><i class="fas fa-boxes me-1"></i> Rekap per Barang</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th class="text-center">Satuan</th>
                                        <th class="text-center">Qty Masuk</th>
                                        <th class="text-end">Nilai Masuk</th>
                                        <th class="text-center">Qty Keluar</th>
                                        <th class="text-end">Nilai Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($rekapBarang as $b)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $b->kode_barang }}</td>
                                            <td>{{ $b->nama_barang }}</td>
                                            <td class="text-center">{{ $b->satuan }}</td>
                                            <td class="text-center text-success fw-semibold">{{ $b->qty_masuk }}</td>
                                            <td class="text-end">Rp {{ number_format($b->nilai_masuk, 0, ',', '.') }}</td>
                                            <td class="text-center text-danger fw-semibold">{{ $b->qty_keluar }}</td>
                                            <td class="text-end">Rp {{ number_format($b->nilai_keluar, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="text-center text-muted py-3">Tidak ada pergerakan barang pada periode ini.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sel = document.getElementById('sel-periode');
            function togglePeriode() {
                var bulanan = sel.value === 'bulanan';
                document.getElementById('wrap-tanggal').classList.toggle('d-none', bulanan);
                document.getElementById('wrap-bulan').classList.toggle('d-none', !bulanan);
                document.getElementById('wrap-tahun').classList.toggle('d-none', !bulanan);
            }
            sel.addEventListener('change', togglePeriode);
            togglePeriode();
        });
    </script>
@endpush
