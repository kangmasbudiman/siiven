@extends('_layouts.dashboard')
@section('title', 'Form Perawatan Barang')

@section('nav')
    <div class="col-sm-8">
        <div class="page-header float-right">
            <div class="page-title">
                <ol class="breadcrumb text-right">
                    <li><a href="{{ route('home') }}">Dashboard</a></li>
                    <li><a href="{{ route('perawatan.scan') }}">Scan</a></li>
                    <li class="active">Form Perawatan</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- Kolom utama: form --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fa fa-wrench me-2"></i>Form Perawatan Barang</h4>
                    <a href="{{ route('perawatan.scan') }}" class="btn btn-light btn-sm">
                        <i class="fa fa-arrow-left me-1"></i>Scan Lagi
                    </a>
                </div>
                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    {{-- Info Barang --}}
                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-light py-2">
                            <strong><i class="fa fa-box me-1"></i>Informasi Barang</strong>
                        </div>
                        <div class="card-body py-3">
                            <div class="row small">
                                <div class="col-md-6 mb-2">
                                    <span class="text-muted d-block">Nama Barang</span>
                                    <strong>{{ $stock->barang->nama_barang ?? '-' }}</strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <span class="text-muted d-block">Kode Barang</span>
                                    <strong>{{ $stock->barang->kode_barang ?? '-' }}</strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <span class="text-muted d-block">Nomor Inventaris</span>
                                    <strong class="text-primary">{{ $stock->nomorInventaris }}</strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <span class="text-muted d-block">Lokasi Ruangan</span>
                                    <strong>{{ $stock->ruangan->nama_ruangan ?? '-' }}</strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <span class="text-muted d-block">Merk / Type</span>
                                    <strong>{{ $stock->merk ?? '-' }} / {{ $stock->type ?? '-' }}</strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <span class="text-muted d-block">Nomor Seri</span>
                                    <strong>{{ $stock->nomorSeri ?? '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('perawatan.store', $stock->id) }}"
                          enctype="multipart/form-data" id="form-perawatan">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control"
                                       value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jam <span class="text-danger">*</span></label>
                                <input type="time" name="jam" class="form-control"
                                       value="{{ old('jam', date('H:i')) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Teknisi</label>
                                <input type="text" class="form-control bg-light"
                                       value="{{ auth()->user()->nama }}" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Perawatan <span class="text-danger">*</span></label>
                                <select name="jenis_perawatan" class="form-select" required>
                                    @foreach(\App\Models\PerawatanBarang::$jenisLabels as $val => $meta)
                                        <option value="{{ $val }}" {{ old('jenis_perawatan') == $val ? 'selected' : '' }}>
                                            <i class="fa {{ $meta['icon'] }}"></i> {{ $meta['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @foreach(\App\Models\PerawatanBarang::$statusLabels as $val => $meta)
                                        <option value="{{ $val }}" {{ old('status', 'selesai') == $val ? 'selected' : '' }}>
                                            {{ $meta['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" rows="3" class="form-control"
                                          placeholder="Catatan pekerjaan, kondisi ditemukan, tindakan yang dilakukan...">{{ old('keterangan') }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Foto Sebelum (opsional)</label>
                                <input type="file" name="foto_sebelum" class="form-control" accept="image/*">
                                <small class="text-muted">Maksimal 5MB. JPG/PNG.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Foto Sesudah (opsional)</label>
                                <input type="file" name="foto_sesudah" class="form-control" accept="image/*">
                                <small class="text-muted">Maksimal 5MB. JPG/PNG.</small>
                            </div>
                        </div>

                        {{-- Tanda Tangan --}}
                        <div class="card border-warning mb-3">
                            <div class="card-header bg-warning bg-opacity-25 py-2">
                                <strong><i class="fa fa-pencil me-1"></i>Tanda Tangan Teknisi <span class="text-danger">*</span></strong>
                            </div>
                            <div class="card-body">
                                <canvas id="signature-pad" class="border rounded"
                                        style="width:100%; height:200px; touch-action:none; cursor:crosshair; background:#fff;"></canvas>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">Tanda tangan di atas canvas menggunakan mouse/trackpad/layar sentuh.</small>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="btn-clear">
                                        <i class="fa fa-eraser me-1"></i>Clear
                                    </button>
                                </div>
                                <input type="hidden" name="ttd_teknisi" id="ttd-input">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('perawatan.scan') }}" class="btn btn-secondary">
                                <i class="fa fa-times me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="btn-submit">
                                <i class="fa fa-save me-1"></i>Simpan Perawatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Kolom sidebar: riwayat --}}
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong><i class="fa fa-history me-1"></i>Riwayat Perawatan</strong>
                    <a href="{{ route('perawatan.history', $stock->id) }}" class="btn btn-link btn-sm p-0">
                        Lihat semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($riwayat->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-2x mb-2"></i><br>
                            Belum ada riwayat perawatan untuk barang ini.
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($riwayat as $r)
                                <li class="list-group-item py-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="badge bg-{{ $r->jenis_class }} text-white">
                                            <i class="fa {{ $r->jenis_perawatan ? \App\Models\PerawatanBarang::$jenisLabels[$r->jenis_perawatan]['icon'] ?? 'fa-wrench' : '' }}"></i>
                                            {{ $r->jenis_label }}
                                        </span>
                                        <small class="text-muted">{{ $r->tanggal->format('d/m/Y') }}</small>
                                    </div>
                                    <div class="small mt-1">{{ $r->teknisi->nama ?? 'N/A' }}</div>
                                    @if($r->keterangan)
                                        <div class="small text-muted mt-1">{{ Str::limit($r->keterangan, 80) }}</div>
                                    @endif
                                    <a href="{{ route('perawatan.show', $r->id) }}" class="small">Detail →</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Signature Pad library --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    const canvas = document.getElementById('signature-pad');
    const ttdInput = document.getElementById('ttd-input');
    const pad = new SignaturePad(canvas, {
        backgroundColor: '#ffffff',
        penColor: '#0d47a1',
        minWidth: 0.7,
        maxWidth: 2.5,
    });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        pad.clear();
    }
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    document.getElementById('btn-clear').addEventListener('click', () => pad.clear());

    document.getElementById('form-perawatan').addEventListener('submit', (e) => {
        if (pad.isEmpty()) {
            e.preventDefault();
            alert('Mohon isi tanda tangan terlebih dahulu.');
            return;
        }
        ttdInput.value = pad.toDataURL('image/png');
    });
</script>
@endsection
