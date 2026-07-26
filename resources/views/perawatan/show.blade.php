@extends('_layouts.dashboard')
@section('title', 'Detail Perawatan')

@section('nav')
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li><a href="{{ route('home') }}">Dashboard</a></li>
                    <li><a href="{{ route('perawatan.history', $perawatan->stock_barang_id) }}">Riwayat</a></li>
                    <li class="active">Detail Perawatan</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fa fa-clipboard-check me-2"></i>Detail Perawatan Barang</h4>
                    <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                        <i class="fa fa-print me-1"></i>Print
                    </button>
                </div>
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    {{-- Header laporan --}}
                    <div class="text-center mb-3">
                        <h5 class="mb-0">LAPORAN PERAWATAN BARANG</h5>
                        <small class="text-muted">Tanggal: {{ $perawatan->tanggal->format('d/m/Y') }} • Jam: {{ $perawatan->jam }}</small>
                    </div>

                    {{-- Info Barang --}}
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-light py-2"><strong><i class="fa fa-box me-1"></i>Identitas Barang</strong></div>
                        <div class="card-body py-2">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td style="width:160px" class="text-muted">Nama Barang</td>
                                    <td>: <strong>{{ $perawatan->stockBarang->barang->nama_barang ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kode Barang</td>
                                    <td>: {{ $perawatan->stockBarang->barang->kode_barang ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nomor Inventaris</td>
                                    <td>: <strong class="text-primary">{{ $perawatan->stockBarang->nomorInventaris }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Lokasi Ruangan</td>
                                    <td>: {{ $perawatan->stockBarang->ruangan->nama_ruangan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Merk / Type / No. Seri</td>
                                    <td>: {{ $perawatan->stockBarang->merk ?? '-' }} / {{ $perawatan->stockBarang->type ?? '-' }} / {{ $perawatan->stockBarang->nomorSeri ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Detail Pekerjaan --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light py-2"><strong><i class="fa fa-wrench me-1"></i>Detail Pekerjaan</strong></div>
                        <div class="card-body py-2">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td style="width:160px" class="text-muted">Jenis Perawatan</td>
                                    <td>:
                                        <span class="badge bg-{{ $perawatan->jenis_class }}">
                                            <i class="fa {{ \App\Models\PerawatanBarang::$jenisLabels[$perawatan->jenis_perawatan]['icon'] ?? 'fa-wrench' }}"></i>
                                            {{ $perawatan->jenis_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status</td>
                                    <td>: <span class="badge bg-{{ $perawatan->status_class }}">{{ $perawatan->status_label }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal & Jam</td>
                                    <td>: {{ $perawatan->tanggal->format('d/m/Y') }} {{ $perawatan->jam }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Keterangan</td>
                                    <td>: {{ $perawatan->keterangan ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Foto --}}
                    @if($perawatan->foto_sebelum || $perawatan->foto_sesudah)
                        <div class="card mb-3">
                            <div class="card-header bg-light py-2"><strong><i class="fa fa-photo me-1"></i>Dokumentasi</strong></div>
                            <div class="card-body">
                                <div class="row">
                                    @if($perawatan->foto_sebelum)
                                        <div class="col-md-6 text-center">
                                            <small class="text-muted d-block mb-1">SEBELUM</small>
                                            <img src="{{ asset('storage/'.$perawatan->foto_sebelum) }}" class="img-fluid rounded border" style="max-height:240px">
                                        </div>
                                    @endif
                                    @if($perawatan->foto_sesudah)
                                        <div class="col-md-6 text-center">
                                            <small class="text-muted d-block mb-1">SETELAH</small>
                                            <img src="{{ asset('storage/'.$perawatan->foto_sesudah) }}" class="img-fluid rounded border" style="max-height:240px">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- TTD --}}
                    <div class="card border-warning mb-3">
                        <div class="card-header bg-light py-2"><strong><i class="fa fa-pencil me-1"></i>Ditandatangani oleh Teknisi</strong></div>
                        <div class="card-body text-center">
                            <img src="data:image/png;base64,{{ $perawatan->ttd_teknisi }}"
                                 style="max-height:120px" alt="TTD">
                            <div class="mt-2">
                                <strong>{{ $perawatan->teknisi->nama ?? 'N/A' }}</strong>
                                <div class="small text-muted">{{ $perawatan->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @@media print {
        .card-header.bg-primary, .btn { display:none !important; }
        .card { border:1px solid #000 !important; box-shadow:none !important; }
        body { background:#fff !important; }
    }
</style>
@endsection
