@extends('_layouts.dashboard')
@section('title', 'Riwayat Perawatan Barang')

@section('nav')
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li><a href="{{ route('home') }}">Dashboard</a></li>
                    <li><a href="{{ route('perawatan.scan') }}">Scan</a></li>
                    <li class="active">Riwayat Perawatan</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fa fa-history me-2"></i>Riwayat Perawatan</h4>
            <a href="{{ route('perawatan.create', $stock->id) }}" class="btn btn-light btn-sm">
                <i class="fa fa-plus me-1"></i>Tambah Perawatan
            </a>
        </div>

        <div class="card-body">
            <div class="card mb-3 border-info">
                <div class="card-body py-3">
                    <div class="row small">
                        <div class="col-md-4"><span class="text-muted d-block">Nama Barang</span><strong>{{ $stock->barang->nama_barang ?? '-' }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block">Nomor Inventaris</span><strong>{{ $stock->nomorInventaris }}</strong></div>
                        <div class="col-md-4"><span class="text-muted d-block">Ruangan</span><strong>{{ $stock->ruangan->nama_ruangan ?? '-' }}</strong></div>
                    </div>
                </div>
            </div>

            <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Jenis</label>
                    <select name="jenis" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach(\App\Models\PerawatanBarang::$jenisLabels as $val => $meta)
                            <option value="{{ $val }}" {{ request('jenis') == $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="fa fa-filter me-1"></i>Filter</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Teknisi</th>
                            <th>Keterangan</th>
                            <th style="width:80px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $i => $r)
                            <tr>
                                <td>{{ ($riwayat->currentPage() - 1) * $riwayat->perPage() + $i + 1 }}</td>
                                <td>{{ $r->tanggal->format('d/m/Y') }}</td>
                                <td>{{ $r->jam }}</td>
                                <td>
                                    <span class="badge bg-{{ $r->jenis_class }}">
                                        <i class="fa {{ \App\Models\PerawatanBarang::$jenisLabels[$r->jenis_perawatan]['icon'] ?? 'fa-wrench' }}"></i>
                                        {{ $r->jenis_label }}
                                    </span>
                                </td>
                                <td><span class="badge bg-{{ $r->status_class }}">{{ $r->status_label }}</span></td>
                                <td>{{ $r->teknisi->nama ?? '-' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($r->keterangan, 60) }}</td>
                                <td>
                                    <a href="{{ route('perawatan.show', $r->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fa fa-inbox fa-2x mb-2"></i><br>
                                    Tidak ada riwayat perawatan untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $riwayat->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
