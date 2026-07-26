@extends('_layouts.dashboard')
@section('title', 'Print Barcode Barang')

@section('nav')
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="active">Print Barcode</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fa fa-qrcode mr-2"></i>Label Barcode Barang</h4>
            <div>
                <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                    <i class="fa fa-print mr-1"></i>Print
                </button>
            </div>
        </div>
        <div class="card-body text-center">

            <div class="d-inline-block border p-4 bg-white" id="label-area" style="max-width:380px;">
                <div class="text-center mb-2">
                    <strong>{{ strtoupper(site('nama_toko')) }}</strong>
                    <div><small>Rumah Sakit</small></div>
                </div>

                <div class="row text-center mb-2">
                    <div class="col-6">
                        @php
                            $qrInternal = DNS2D::getBarcodePNG($stock->nomorInventaris, 'QRCODE', 5, 5);
                        @endphp
                        <img src="data:image/png;base64,{{ $qrInternal }}"
                             style="width:150px; height:150px;" alt="QR Internal">
                        <div class="small mt-1"><i class="fa fa-wrench mr-1"></i><strong>Teknisi</strong></div>
                        <div class="small text-muted">Scan di sistem internal</div>
                    </div>
                    <div class="col-6">
                        @php
                            $urlPublik = rtrim(config('app.url', request()->getSchemeAndHttpHost()), '/') . '/public/perawatan/scan?code=' . urlencode($stock->nomorInventaris);
                            $qrPublik = DNS2D::getBarcodePNG($urlPublik, 'QRCODE', 5, 5);
                        @endphp
                        <img src="data:image/png;base64,{{ $qrPublik }}"
                             style="width:150px; height:150px;" alt="QR Publik">
                        <div class="small mt-1"><i class="fa fa-users mr-1"></i><strong>Umum</strong></div>
                        <div class="small text-muted">Scan pakai HP biasa</div>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="small text-muted">Nomor Inventaris</div>
                    <strong style="font-size:14px; word-break:break-all;">{{ $stock->nomorInventaris }}</strong>
                </div>

                <hr class="my-2">

                <div class="text-left small">
                    <div><strong>{{ $stock->barang->nama_barang ?? '-' }}</strong></div>
                    <div>Merk: {{ $stock->merk ?? '-' }}</div>
                    <div>Type: {{ $stock->type ?? '-' }}</div>
                    <div>No. Seri: {{ $stock->nomorSeri ?? '-' }}</div>
                    <div>Ruangan: {{ $stock->ruangan->nama_ruangan ?? '-' }}</div>
                </div>
            </div>

            <hr class="my-4">

            <div class="alert alert-info text-left no-print">
                <strong><i class="fa fa-info-circle mr-1"></i>Petunjuk Print:</strong>
                <ol class="mb-0 mt-1">
                    <li>Gunakan kertas label (stiker) ukuran minimal 5x5 cm.</li>
                    <li>Klik tombol <strong>Print</strong>, pilih printer label.</li>
                    <li>Set "Margins: None" dan "Scale: 100%" di dialog print.</li>
                    <li>Setelah ter-print, tempel label pada bagian tubuh barang yang mudah terlihat & tidak panas.</li>
                    <li>Petugas cukup scan QR ini untuk membuka form perawatan.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<style>
    @@media print {
        body * { visibility: hidden; }
        #label-area, #label-area * { visibility: visible; }
        #label-area { position: absolute; top: 0; left: 0; width: 100%; border: none !important; padding: 8mm !important; }
        .card-header, .btn, .alert, .no-print { display: none !important; }
    }
</style>
@endsection
