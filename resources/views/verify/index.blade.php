<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Tanda Tangan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fa; }
        .verify-card { max-width: 500px; margin: 60px auto; }
        .icon-valid { font-size: 4rem; color: #198754; }
        .icon-invalid { font-size: 4rem; color: #dc3545; }
    </style>
</head>
<body>
<div class="container verify-card">
    @if($approval)
    <div class="card shadow">
        <div class="card-body text-center py-4">
            <div class="icon-valid mb-3">&#10003;</div>
            <h4 class="text-success fw-bold mb-1">Tanda Tangan Digital Terverifikasi</h4>
            <p class="text-muted mb-4">Dokumen ini sah dan telah ditandatangani secara digital</p>

            <div class="text-start bg-light rounded p-3 mb-3">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <th style="width:160px" class="text-muted">Dokumen</th>
                        <td>: <strong>{{ $approval->usulan->nomor_usulan }}</strong></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Unit</th>
                        <td>: {{ $approval->usulan->ruangan->nama_ruangan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Jenis Persetujuan</th>
                        <td>: {{ $approval->level_label }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Disetujui Oleh</th>
                        <td>: <strong>{{ $approval->approver->nama ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Waktu</th>
                        <td>: {{ $approval->approved_at->format('d F Y, H:i:s') }} WIB</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td>: <span class="badge bg-success">SAH & VALID</span></td>
                    </tr>
                </table>
            </div>

            <p class="text-muted small mb-0">
                <i class="fa fa-lock me-1"></i>
                Diverifikasi oleh sistem RS. Royal Prima Jambi
            </p>
        </div>
    </div>
    @else
    <div class="card shadow">
        <div class="card-body text-center py-4">
            <div class="icon-invalid mb-3">&#10007;</div>
            <h4 class="text-danger fw-bold mb-1">Tanda Tangan Tidak Valid</h4>
            <p class="text-muted mb-4">
                QR Code ini tidak valid atau dokumen tidak ditemukan dalam sistem.
            </p>
            <div class="alert alert-warning text-start">
                <strong>Kemungkinan penyebab:</strong>
                <ul class="mb-0 mt-1">
                    <li>QR Code dipalsukan atau dimanipulasi</li>
                    <li>Dokumen telah dihapus dari sistem</li>
                    <li>Link verifikasi rusak</li>
                </ul>
            </div>
            <p class="text-muted small">Hubungi bagian IT RS. Royal Prima Jambi untuk konfirmasi.</p>
        </div>
    </div>
    @endif
</div>
</body>
</html>
