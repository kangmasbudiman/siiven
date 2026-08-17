@extends('_layouts.app')

@section('title', 'Logistik - Stok Gudang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-warehouse me-2"></i>
                            Stok Gudang Logistik
                            @if ($ruangan)
                                <small class="text-muted">({{ $ruangan->kode_ruangan }} - {{ $ruangan->nama_ruangan }})</small>
                            @endif
                        </h4>
                        @if ($ruangan)
                            <div class="btn-group">
                                <a href="{{ route('logistik.pemasukan.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-arrow-down me-1"></i> Pemasukan
                                </a>
                                <a href="{{ route('logistik.pengeluaran.create') }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-arrow-up me-1"></i> Pengeluaran
                                </a>
                                <a href="{{ route('logistik.riwayat') }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-history me-1"></i> Riwayat
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @if (!empty($errorRuangan))
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-exclamation-triangle me-1"></i> {{ $errorRuangan }}
                            </div>
                        @else
                            <table id="stok-logistik-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Merk</th>
                                        <th>Type</th>
                                        <th class="text-center">Jumlah</th>
                                        <th>Satuan</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Nilai Stok</th>
                                        <th>Kondisi</th>
                                        <th>No. Inventaris</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($stoks as $stok)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $stok->barang->kode_barang }}</td>
                                            <td>{{ $stok->barang->nama_barang }}</td>
                                            <td>{{ $stok->merk ?? '-' }}</td>
                                            <td>{{ $stok->type ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $stok->jumlah > 0 ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $stok->jumlah }}
                                                </span>
                                            </td>
                                            <td>{{ $stok->barang->satuan }}</td>
                                            <td class="text-end">{{ $stok->harga ? 'Rp ' . number_format($stok->harga, 0, ',', '.') : '-' }}</td>
                                            <td class="text-end">{{ $stok->harga ? 'Rp ' . number_format($stok->harga * $stok->jumlah, 0, ',', '.') : '-' }}</td>
                                            <td>
                                                <span class="badge bg-info text-dark">{{ $stok->kondisi->nama_kondisi }}</span>
                                            </td>
                                            <td><small>{{ $stok->nomorInventaris }}</small></td>
                                            <td>{{ $stok->keterangan ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center text-muted py-4">
                                                Belum ada stok barang di gudang logistik.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            if (typeof $.fn.dataTable !== 'undefined') {
                $('#stok-logistik-table').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    info: true,
                    autoWidth: false,
                    pageLength: 10,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });
            }
        });
    </script>
@endpush
