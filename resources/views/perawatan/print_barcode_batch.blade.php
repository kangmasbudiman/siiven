@extends('_layouts.dashboard')
@section('title', 'Cetak Massal Barcode')

@section('nav')
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="active">Cetak Massal Barcode</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fa fa-print mr-2"></i>Cetak Massal Barcode</h4>
            <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                <i class="fa fa-print mr-1"></i>Print Semua
            </button>
        </div>
        <div class="card-body">

            <form method="GET" action="{{ route('perawatan.barcode.batch') }}" class="form-inline row mb-3 align-items-end">
                <div class="col-md-6">
                    <label class="small mb-1 d-block">Filter berdasarkan Ruangan</label>
                    <select name="ruangan_id" class="form-control form-control-sm" style="max-width:400px;">
                        <option value="">-- Semua Ruangan --</option>
                        @foreach($ruangans as $r)
                            <option value="{{ $r->id }}" {{ (string)request('ruangan_id') === (string)$r->id ? 'selected' : '' }}>
                                {{ $r->kode_ruangan }} - {{ $r->nama_ruangan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mt-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fa fa-filter mr-1"></i>Filter
                    </button>
                    @if(request('ruangan_id'))
                        <a href="{{ route('perawatan.barcode.batch') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-times mr-1"></i>Reset
                        </a>
                    @endif
                </div>
            </form>

            @if(session('error'))
                <div class="alert alert-danger py-2 small">{{ session('error') }}</div>
            @endif

            <div class="alert alert-info py-2 small no-print">
                <i class="fa fa-info-circle mr-1"></i>
                <strong>Dual QR Code:</strong> QR kiri = untuk teknisi (scan di sistem internal, wajib login) &middot;
                QR kanan = untuk umum (scan pakai HP biasa, langsung ke halaman riwayat).
                Total: <strong>{{ $stocks->count() }}</strong> item siap dicetak.
                @if(request('ruangan_id'))
                    &middot; Filter: <strong>{{ optional(\App\Models\Ruangan::find(request('ruangan_id')))->nama_ruangan }}</strong>
                @endif
            </div>

            <div id="batch-area">
                @if($stocks->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fa fa-inbox fa-2x mb-2"></i><br>
                        Tidak ada barang dengan nomor inventaris pada filter ini.
                    </div>
                @else
                    <div class="row">
                        @foreach($stocks as $stock)
                            @php
                                $kodeInv = trim((string)($stock->nomorInventaris ?? ''));
                            @endphp
                            @if($kodeInv === '')
                                @continue
                            @endif
                            @php
                                $appUrl = config('app.url', '');
                                $isLocal = empty($appUrl) || stripos($appUrl, 'localhost') !== false || strpos($appUrl, '127.0.0.1') !== false;
                                $base = $isLocal ? request()->getSchemeAndHttpHost() : rtrim($appUrl, '/');
                                $urlPublik = $base . '/public/perawatan/scan?code=' . urlencode($kodeInv);
                            @endphp
                            <div class="col-12 col-sm-6 col-md-4 mb-3">
                                <div class="border p-3 bg-white text-center label-box" style="page-break-inside: avoid;">
                                    <div class="small font-weight-bold text-uppercase mb-2">{{ site('nama_toko') }}</div>
                                    <div class="d-flex justify-content-around align-items-stretch mb-2">
                                        <div class="text-center qr-frame">
                                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($kodeInv, 'QRCODE', 3, 3) }}"
                                                 style="width:90px; height:90px;" alt="QR Internal">
                                            <div style="font-size:8px; line-height:1.1;" class="text-muted mt-1 font-weight-bold">
                                                <i class="fa fa-wrench"></i> TEKNISI
                                            </div>
                                        </div>
                                        <div class="text-center qr-frame">
                                            <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($urlPublik, 'QRCODE', 3, 3) }}"
                                                 style="width:90px; height:90px;" alt="QR Publik">
                                            <div style="font-size:8px; line-height:1.1;" class="text-muted mt-1 font-weight-bold">
                                                <i class="fa fa-users"></i> UMUM
                                            </div>
                                        </div>
                                    </div>
                                    <div style="font-size:9px; word-break:break-all; line-height:1.2;">{{ $kodeInv }}</div>
                                    <hr class="my-2">
                                    <div style="font-size:11px" class="font-weight-bold">{{ \Illuminate\Support\Str::limit($stock->barang->nama_barang ?? '-', 32) }}</div>
                                    <div style="font-size:10px" class="text-muted">{{ $stock->ruangan->nama_ruangan ?? '' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .qr-frame {
        border: 1.5px solid #333 !important;
        border-radius: 8px;
        padding: 8px 6px 4px;
        margin: 0 6px;
        background: #fff;
        min-width: 105px;
    }
    .label-box {
        border: 2px solid #0d47a1 !important;
        border-radius: 10px;
    }
    @@media print {
        body * { visibility: hidden; }
        #batch-area, #batch-area * { visibility: visible; }
        #batch-area { position: absolute; top: 0; left: 0; width: 100%; }
        .card-header, .btn, .alert, form, .no-print { display: none !important; }
        .label-box {
            border: 1.5px solid #0d47a1 !important;
            page-break-inside: avoid;
        }
        .qr-frame {
            border: 1px solid #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
@endsection
