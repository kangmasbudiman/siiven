@extends('_layouts.dashboard')
@section('title', 'Scan Barcode Maintenance')

@section('nav')
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="active">Scan Barcode</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fa fa-qrcode me-2"></i>Scan Barcode Barang</h4>
                </div>
                <div class="card-body">

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fa fa-exclamation-circle me-1"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <p class="text-muted">
                        Arahkan barcode scanner ke label barang, atau ketik / tempel nomor inventaris
                        lalu tekan <kbd>Enter</kbd>. Anda juga bisa scan pakai kamera HP dengan menekan tombol kamera.
                    </p>

                    <form method="POST" action="{{ route('perawatan.lookup') }}" id="form-scan">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nomor Inventaris / Hasil Scan</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                                <input type="text" name="code" id="scan-input"
                                       class="form-control form-control-lg"
                                       placeholder="Contoh: RPJ/1/ICU/20260101120000"
                                       autocomplete="off"
                                       autofocus
                                       value="{{ old('code') }}">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-search me-1"></i>Cari
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-info btn-lg" id="btn-camera">
                            <i class="fa fa-camera me-1"></i>Scan pakai Kamera HP / Webcam
                        </button>
                    </div>

                    <div id="camera-wrap" class="mt-3" style="display:none;">
                        <div class="card border-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong><i class="fa fa-camera me-1"></i>Kamera Scanner</strong>
                                    <button type="button" class="btn-close" id="btn-stop-camera" aria-label="Close"></button>
                                </div>
                                <div id="qr-reader" style="width:100%;"></div>
                                <small class="text-muted d-block mt-2">
                                    Arahkan kamera ke QR code. Otomatis redirect jika cocok.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fa fa-info-circle me-1 text-info"></i>Petunjuk Penggunaan</h6>
                    <ol class="mb-0 small">
                        <li>Pastikan barang sudah memiliki label QR code (cetak lewat menu <strong>Print Barcode</strong>).</li>
                        <li>Lakukan scan menggunakan barcode scanner USB atau kamera HP.</li>
                        <li>Setelah scan berhasil, Anda akan diarahkan ke form input perawatan.</li>
                        <li>Isi tanggal, jam, jenis perawatan, keterangan, lalu tanda tangan di canvas.</li>
                        <li>Simpan. Riwayat perawatan akan otomatis tersimpan dan dapat dilihat kembali.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- HTML5-QRCode library untuk scan via kamera --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    const input = document.getElementById('scan-input');
    const btnCamera = document.getElementById('btn-camera');
    const btnStop = document.getElementById('btn-stop-camera');
    const wrap = document.getElementById('camera-wrap');
    const readerId = 'qr-reader';
    let html5QrCode = null;

    btnCamera.addEventListener('click', async () => {
        wrap.style.display = 'block';
        if (html5QrCode) return;
        html5QrCode = new Html5Qrcode(readerId);
        try {
            await html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => onScanSuccess(decodedText),
                (err) => {}
            );
        } catch (e) {
            alert('Tidak bisa mengakses kamera. Pastikan browser punya izin kamera.');
            wrap.style.display = 'none';
            html5QrCode = null;
        }
    });

    btnStop.addEventListener('click', async () => {
        if (html5QrCode) {
            await html5QrCode.stop();
            await html5QrCode.clear();
            html5QrCode = null;
        }
        wrap.style.display = 'none';
    });

    function onScanSuccess(text) {
        // Trim & isi input, langsung submit
        input.value = text.trim();
        document.getElementById('form-scan').submit();
    }

    // USB scanner otomatis ketik + Enter; biarkan default behavior submit
    document.getElementById('form-scan').addEventListener('submit', () => {
        input.value = input.value.trim();
    });
</script>
@endsection
