<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex,nofollow">
    <title>Info Maintenance Barang - {{ site('nama_toko') }}</title>
    <link rel="stylesheet" href="{{ asset('sufee-admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('sufee-admin/vendors/font-awesome/css/font-awesome.min.css') }}">
    <style>
        body { background: #f4f7fa; min-height: 100vh; }
        .brand-bar { background: linear-gradient(135deg,#0d47a1,#1976d2); color:#fff; padding:14px 0; }
        .brand-bar h1 { font-size:1.25rem; margin:0; font-weight:600; }
        .scan-card { max-width: 540px; margin: 40px auto; border:0; border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,.08); overflow:hidden; }
        .scan-card .card-header { background:#fff; border-bottom:1px solid #eee; padding:20px 24px; }
        .scan-card .card-header h2 { font-size:1.15rem; margin:0; color:#0d47a1; font-weight:700; }
        .scan-card .card-body { padding:24px; }
        #qr-reader { width:100%; max-width:300px; margin:0 auto; border-radius:10px; overflow:hidden; }
        #qr-reader video { border-radius:10px; }
        .badge-verified { background:#28a745; }
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
    <div class="card scan-card">
        <div class="card-header">
            <h2><i class="fa fa-qrcode mr-2"></i>Cek Riwayat Perawatan Barang</h2>
            <p class="small text-muted mb-0">Pindai QR / barcode pada barang atau ketik nomor inventaris untuk melihat riwayat maintenance.</p>
        </div>
        <div class="card-body">

            @if(session('error'))
                <div class="alert alert-danger py-2">
                    <i class="fa fa-exclamation-circle mr-1"></i>{{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('public.perawatan.lookup') }}" id="form-scan">
                @csrf
                <div class="form-group">
                    <label for="code">Nomor Inventaris</label>
                    <input type="text" id="code" name="code"
                           class="form-control form-control-lg"
                           placeholder="contoh: RPJ/2/Front Office/20260109194417"
                           value="{{ old('code') }}"
                           autofocus
                           autocomplete="off">
                    <small class="form-text text-muted">
                        Arahkan scanner USB ke label, atau ketik manual lalu tekan <kbd>Enter</kbd>.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg mt-2">
                    <i class="fa fa-search mr-1"></i>Cari
                </button>
            </form>

            <hr class="my-4">

            <div class="text-center">
                <button type="button" id="btn-camera" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-camera mr-1"></i>Scan dengan Kamera
                </button>
                <button type="button" id="btn-stop-camera" class="btn btn-outline-danger btn-sm d-none">
                    <i class="fa fa-stop mr-1"></i>Stop Kamera
                </button>
            </div>

            <div id="qr-reader" class="mt-3 d-none"></div>

            <hr class="my-4">

            <div class="alert alert-info py-2 small mb-0">
                <i class="fa fa-info-circle mr-1"></i>
                <strong>Catatan:</strong> Halaman ini hanya untuk melihat riwayat perawatan (read-only).
                Input maintenance baru wajib login sebagai teknisi/teknisi.
            </div>
        </div>
    </div>

    <p class="text-center text-muted small">
        &copy; {{ date('Y') }} {{ site('nama_toko') }} &middot; Sistem Inventori
    </p>
</div>

<script src="{{ asset('sufee-admin/vendors/jquery/jquery.min.js') }}"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode = null;

    document.getElementById('btn-camera').addEventListener('click', function() {
        const reader = document.getElementById('qr-reader');
        reader.classList.remove('d-none');
        document.getElementById('btn-camera').classList.add('d-none');
        document.getElementById('btn-stop-camera').classList.remove('d-none');

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("qr-reader");
        }

        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 220, height: 220 } },
            (decodedText) => {
                document.getElementById('code').value = decodedText;
                html5QrCode.stop().then(() => {
                    document.getElementById('form-scan').submit();
                });
            },
            (err) => { /* ignore */ }
        ).catch(err => {
            alert('Tidak bisa mengakses kamera: ' + err);
        });
    });

    document.getElementById('btn-stop-camera').addEventListener('click', function() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                document.getElementById('qr-reader').classList.add('d-none');
                document.getElementById('btn-camera').classList.remove('d-none');
                document.getElementById('btn-stop-camera').classList.add('d-none');
            });
        }
    });
</script>

</body>
</html>
