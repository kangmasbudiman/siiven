@extends('_layouts.app')

@section('title', 'Dashboard ' . $roleLabel)

@section('content')
<div class="container-fluid mt-4">

    {{-- Header --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">
            <i class="fa fa-dashboard me-2 text-primary"></i>
            Dashboard — {{ $roleLabel }}
        </h4>
        <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->nama }}. Berikut adalah ringkasan aktivitas persetujuan Anda.</p>
    </div>

    {{-- Kartu statistik --}}
    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f39c12 !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:56px;height:56px;background:#fff3cd;">
                            <i class="fa fa-clock-o fa-2x" style="color:#f39c12;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small">Menunggu Persetujuan Saya</div>
                        <div class="fs-2 fw-bold" style="color:#f39c12;">{{ $totalPending }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #27ae60 !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:56px;height:56px;background:#d4edda;">
                            <i class="fa fa-check-circle fa-2x" style="color:#27ae60;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small">Total Disetujui</div>
                        <div class="fs-2 fw-bold" style="color:#27ae60;">{{ $totalApproved }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #e74c3c !important;">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:56px;height:56px;background:#f8d7da;">
                            <i class="fa fa-times-circle fa-2x" style="color:#e74c3c;"></i>
                        </div>
                    </div>
                    <div>
                        <div class="text-muted small">Total Ditolak</div>
                        <div class="fs-2 fw-bold" style="color:#e74c3c;">{{ $totalRejected }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        {{-- Daftar usulan pending --}}
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fa fa-list me-1 text-warning"></i>
                        Usulan Menunggu Persetujuan Saya
                        @if($totalPending > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ $totalPending }}</span>
                        @endif
                    </h6>
                    <a href="{{ route('usulan.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($pendingUsulans->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="fa fa-check-circle fa-3x mb-3" style="color:#27ae60;"></i>
                            <p class="mb-0">Tidak ada usulan yang menunggu persetujuan Anda.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Usulan</th>
                                        <th>Unit / Ruangan</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingUsulans as $usulan)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">{{ $usulan->nomor_usulan }}</span>
                                        </td>
                                        <td>{{ $usulan->ruangan->nama_ruangan ?? '-' }}</td>
                                        <td>{{ $usulan->nama_penanggung_jawab }}</td>
                                        <td>{{ $usulan->tanggal_pengajuan->format('d/m/Y') }}</td>
                                        <td>Rp {{ number_format($usulan->total, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('usulan.show', $usulan->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye me-1"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Riwayat persetujuan terbaru --}}
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fa fa-history me-1 text-secondary"></i>
                        Riwayat Persetujuan Terakhir
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($riwayatApproval->isEmpty())
                        <div class="text-center text-muted py-5">
                            <i class="fa fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0 small">Belum ada riwayat.</p>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($riwayatApproval as $approval)
                            <li class="list-group-item px-3 py-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold small">
                                            {{ $approval->usulan->nomor_usulan ?? '-' }}
                                        </div>
                                        <div class="text-muted" style="font-size:12px;">
                                            {{ $approval->usulan->ruangan->nama_ruangan ?? '-' }}
                                        </div>
                                    </div>
                                    <span class="badge {{ $approval->status === 'approved' ? 'bg-success' : 'bg-danger' }} ms-2">
                                        {{ $approval->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                    </span>
                                </div>
                                @if($approval->approved_at)
                                <div class="text-muted mt-1" style="font-size:11px;">
                                    <i class="fa fa-clock-o me-1"></i>
                                    {{ \Carbon\Carbon::parse($approval->approved_at)->format('d/m/Y H:i') }}
                                </div>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
