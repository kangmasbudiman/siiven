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
                Total: <strong>{{ $stocks->count() }}</strong> item siap dicetak.
                @if(request('ruangan_id'))
                    Filter aktif: <strong>{{ optional(\App\Models\Ruangan::find(request('ruangan_id')))->nama_ruangan }}</strong>
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
                            <div class="col-12 col-sm-6 col-md-3 mb-3">
                                <div class="border p-2 bg-white text-center label-box" style="page-break-inside: avoid;">
                                    <div class="small font-weight-bold text-uppercase">{{ site('nama_toko') }}</div>
                                    <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG($kodeInv, 'QRCODE', 4, 4) }}"
                                         style="width:130px; height:130px;" alt="QR">
                                    <div style="font-size:10px; word-break:break-all;">{{ $kodeInv }}</div>
                                    <hr class="my-1">
                                    <div style="font-size:11px"><strong>{{ \Illuminate\Support\Str::limit($stock->barang->nama_barang ?? '-', 28) }}</strong></div>
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
    @@media print {
        body * { visibility: hidden; }
        #batch-area, #batch-area * { visibility: visible; }
        #batch-area { position: absolute; top: 0; left: 0; width: 100%; }
        .card-header, .btn, .alert, form, .no-print { display: none !important; }
        .label-box { border: 0.5px dashed #999 !important; }
    }
</style>
@endsection
