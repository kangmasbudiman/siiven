<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex,nofollow">
    <title>Riwayat Maintenance - {{ $stock->barang->nama_barang ?? 'Barang' }}</title>
    <link rel="stylesheet" href="{{ asset('sufee-admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('sufee-admin/vendors/font-awesome/css/font-awesome.min.css') }}">
    <style>
        body { background: #f4f7fa; min-height: 100vh; }
        .brand-bar { background: linear-gradient(135deg,#0d47a1,#1976d2); color:#fff; padding:14px 0; }
        .brand-bar h1 { font-size:1.25rem; margin:0; font-weight:600; }
        .content-card { max-width: 900px; margin: 30px auto; border:0; border-radius:14px; box-shadow:0 8px 24px rgba(0,0,0,.06); overflow:hidden; }
        .stat-box { background:#f8fafc; border-radius:10px; padding:16px; text-align:center; }
        .stat-box .num { font-size:1.6rem; font-weight:700; color:#0d47a1; line-height:1; }
        .stat-box .lbl { font-size:.75rem; color:#64748b; text-transform:uppercase; letter-spacing:.04em; }
        .timeline-item { padding-left:32px; position:relative; padding-bottom:18px; border-left:2px solid #e2e8f0; margin-left:8px; }
        .timeline-item:last-child { border-left-color:transparent; padding-bottom:0; }
        .timeline-item::before {
            content:''; position:absolute; left:-9px; top:2px; width:16px; height:16px;
            border-radius:50%; background:#1976d2; border:3px solid #fff; box-shadow:0 0 0 2px #1976d2;
        }
        .timeline-item.status-pending::before    { background:#ffc107; box-shadow:0 0 0 2px #ffc107; }
        .timeline-item.status-bermasalah::before { background:#dc3545; box-shadow:0 0 0 2px #dc3545; }
        .foto-thumb { width:100%; max-width:140px; height:100px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="brand-bar">
    <div class="container d-flex align-items-center justify-content-between">
        <h1><i class="fa fa-hospital-o mr-2"></i>{{ site('nama_toko') }}</h1>
        <span class="small opacity-75">Informasi Maintenance Barang</span>
    </div>
</div>

<div class="container">
    <div class="card content-card">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="h5 mb-0 text-primary font-weight-bold">
                    <i class="fa fa-wrench mr-2"></i>{{ \Illuminate\Support\Str::limit($stock->barang->nama_barang ?? 'Barang Tidak Dikenal', 50) }}
                </h2>
                <a href="{{ route('public.perawatan.scan') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-qrcode mr-1"></i>Scan Barang Lain
                </a>
            </div>
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td style="width:140px" class="text-muted small">Nomor Inventaris</td>
                            <td><strong style="word-break:break-all">{{ $stock->nomorInventaris }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Kode Barang</td>
                            <td>{{ $stock->barang->kode_barang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Ruangan</td>
                            <td>{{ $stock->ruangan->nama_ruangan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Kondisi</td>
                            <td>{{ $stock->kondisi->nama_kondisi ?? '-' }}</td>
                        </tr>
                        @if(!empty($stock->merk) || !empty($stock->type))
                        <tr>
                            <td class="text-muted small">Merk / Type</td>
                            <td>{{ trim(($stock->merk ?? '') . ' / ' . ($stock->type ?? ''), ' / ') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="num">{{ $totalPerawatan }}</div>
                                <div class="lbl">Total Perawatan</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                @php
                                    $statusClass = [
                                        'selesai'    => 'text-success',
                                        'pending'    => 'text-warning',
                                        'bermasalah' => 'text-danger',
                                    ][$terakhir->status ?? ''] ?? 'text-secondary';
                                @endphp
                                <div class="num {{ $statusClass }}">
                                    @if($terakhir)
                                        {{ \App\Models\PerawatanBarang::$statusLabels[$terakhir->status]['label'] ?? $terakhir->status }}
                                    @else
                                        -
                                    @endif
                                </div>
                                <div class="lbl">Status Terakhir</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-box">
                                @if($terakhir)
                                    <div class="num" style="font-size:1.1rem">
                                        {{ \Carbon\Carbon::parse($terakhir->tanggal)->format('d M Y') }}
                                    </div>
                                    <div class="lbl">Maintenance Terakhir</div>
                                @else
                                    <div class="num text-muted" style="font-size:1.1rem">Belum pernah</div>
                                    <div class="lbl">Maintenance</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <h5 class="text-primary mt-2 mb-3">
                <i class="fa fa-history mr-1"></i>Riwayat Maintenance
            </h5>

            @if($riwayat->isEmpty())
                <div class="text-center text-muted py-4">
                    <i class="fa fa-inbox fa-3x mb-2 d-block opacity-50"></i>
                    <p>Belum ada catatan maintenance untuk barang ini.</p>
                </div>
            @else
                <div class="timeline">
                    @foreach($riwayat as $row)
                        @php
                            $jenisLabel = \App\Models\PerawatanBarang::$jenisLabels[$row->jenis_perawatan]['label'] ?? $row->jenis_perawatan;
                            $jenisClass = \App\Models\PerawatanBarang::$jenisLabels[$row->jenis_perawatan]['class'] ?? 'info';
                            $statusLabel = \App\Models\PerawatanBarang::$statusLabels[$row->status]['label'] ?? $row->status;
                            $statusClass = \App\Models\PerawatanBarang::$statusLabels[$row->status]['class'] ?? 'secondary';
                        @endphp
                        <div class="timeline-item status-{{ $row->status }}">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div>
                                    <span class="badge badge-{{ $jenisClass }}">{{ $jenisLabel }}</span>
                                    <span class="badge badge-{{ $statusClass }}">{{ $statusLabel }}</span>
                                </div>
                                <small class="text-muted">
                                    <i class="fa fa-calendar mr-1"></i>{{ \Carbon\Carbon::parse($row->tanggal)->format('d M Y') }}
                                    &middot; <i class="fa fa-clock-o mr-1"></i>{{ $row->jam }}
                                </small>
                            </div>

                            @if($row->keterangan)
                                <p class="mt-2 mb-1 small">{{ $row->keterangan }}</p>
                            @endif

                            <div class="row mt-2 small text-muted">
                                <div class="col-sm-6">
                                    <i class="fa fa-user mr-1"></i>
                                    Teknisi: <strong>{{ $row->teknisi->nama ?? '-' }}</strong>
                                    @if($row->teknisi && $row->teknisi->jabatan)
                                        <span class="text-muted">({{ $row->teknisi->jabatan }})</span>
                                    @endif
                                </div>
                            </div>

                            @if($row->foto_sebelum || $row->foto_sesudah)
                                <div class="row mt-2">
                                    @if($row->foto_sebelum && \Storage::disk('public')->exists($row->foto_sebelum))
                                        <div class="col-6 col-md-3 mb-2">
                                            <small class="text-muted d-block">Sebelum:</small>
                                            <img src="{{ asset('storage/' . $row->foto_sebelum) }}" class="foto-thumb" alt="Foto sebelum">
                                        </div>
                                    @endif
                                    @if($row->foto_sesudah && \Storage::disk('public')->exists($row->foto_sesudah))
                                        <div class="col-6 col-md-3 mb-2">
                                            <small class="text-muted d-block">Sesudah:</small>
                                            <img src="{{ asset('storage/' . $row->foto_sesudah) }}" class="foto-thumb" alt="Foto sesudah">
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if($row->ttd_teknisi)
                                <details class="mt-1">
                                    <summary class="small text-muted" style="cursor:pointer">Lihat TTD Teknisi</summary>
                                    <img src="data:image/png;base64,{{ $row->ttd_teknisi }}"
                                         style="max-height:80px; background:#fff; border:1px solid #e2e8f0; border-radius:6px; padding:4px;"
                                         alt="Tanda tangan teknisi">
                                </details>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $riwayat->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>

    <p class="text-center text-muted small mb-4">
        &copy; {{ date('Y') }} {{ site('nama_toko') }} &middot; Halaman ini bersifat read-only
    </p>
</div>

</body>
</html>
