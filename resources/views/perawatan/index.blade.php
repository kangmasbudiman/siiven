@extends('_layouts.dashboard')
@section('title', 'Riwayat Perawatan Barang')

@section('nav')
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="active">Riwayat Perawatan</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fa fa-history mr-2"></i>Riwayat Perawatan Barang</h4>
            <a href="{{ route('perawatan.scan') }}" class="btn btn-light btn-sm">
                <i class="fa fa-qrcode mr-1"></i>Scan Barcode
            </a>
        </div>
        <div class="card-body">

            <form method="GET" action="{{ route('perawatan.index') }}" class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="small mb-1">Cari Barang</label>
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="form-control form-control-sm"
                           placeholder="Nama / kode barang...">
                </div>
                <div class="col-md-2">
                    <label class="small mb-1">Ruangan</label>
                    <select name="ruangan_id" class="form-control form-control-sm">
                        <option value="">-- Semua --</option>
                        @foreach($ruangans as $r)
                            <option value="{{ $r->id }}" {{ (string)request('ruangan_id') === (string)$r->id ? 'selected' : '' }}>
                                {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small mb-1">Jenis</label>
                    <select name="jenis" class="form-control form-control-sm">
                        <option value="">-- Semua --</option>
                        @foreach(\App\Models\PerawatanBarang::$jenisLabels as $val => $data)
                            <option value="{{ $val }}" {{ request('jenis') === $val ? 'selected' : '' }}>{{ $data['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small mb-1">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">-- Semua --</option>
                        @foreach(\App\Models\PerawatanBarang::$statusLabels as $val => $data)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $data['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="small mb-1">Dari</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <label class="small mb-1">Sampai</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <label class="small mb-1">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                        <i class="fa fa-filter"></i>
                    </button>
                </div>
            </form>

            @if(session('error'))
                <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Tanggal</th>
                            <th>Barang</th>
                            <th>Ruangan</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Teknisi</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $i => $row)
                            @php
                                $stock = $row->stockBarang;
                                $barang = $stock->barang ?? null;
                            @endphp
                            <tr>
                                <td>{{ ($riwayat->currentPage() - 1) * $riwayat->perPage() + $loop->iteration }}</td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $row->jam }}</small>
                                </td>
                                <td>
                                    @if($barang)
                                        <div class="font-weight-bold">{{ \Illuminate\Support\Str::limit($barang->nama_barang, 40) }}</div>
                                        <small class="text-muted">{{ $stock->nomorInventaris }}</small>
                                    @else
                                        <span class="text-muted">Barang tidak ditemukan</span>
                                    @endif
                                </td>
                                <td>{{ $stock->ruangan->nama_ruangan ?? '-' }}</td>
                                <td>
                                    @php
                                        $jenisLabel = \App\Models\PerawatanBarang::$jenisLabels[$row->jenis_perawatan]['label'] ?? $row->jenis_perawatan;
                                        $jenisClass = \App\Models\PerawatanBarang::$jenisLabels[$row->jenis_perawatan]['class'] ?? 'info';
                                    @endphp
                                    <span class="badge badge-{{ $jenisClass }}">{{ $jenisLabel }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusLabel = \App\Models\PerawatanBarang::$statusLabels[$row->status]['label'] ?? $row->status;
                                        $statusClass = \App\Models\PerawatanBarang::$statusLabels[$row->status]['class'] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td>
                                    @if($row->teknisi)
                                        <div>{{ $row->teknisi->nama }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('perawatan.show', $row->id) }}"
                                       class="btn btn-info btn-sm" title="Lihat Detail">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @if($stock)
                                        <a href="{{ route('perawatan.history', $stock->id) }}"
                                           class="btn btn-secondary btn-sm" title="Riwayat Barang Ini">
                                            <i class="fa fa-history"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada catatan perawatan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Menampilkan {{ $riwayat->count() }} dari total {{ $riwayat->total() }} record
                </div>
                <div>
                    {{ $riwayat->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
