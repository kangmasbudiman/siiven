@extends('_layouts.app')
@section('title', 'Detail Usulan - ' . $usulan->nomor_usulan)

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">{{ session('warning') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fa fa-file-text me-2"></i>{{ $usulan->nomor_usulan }}
                <span class="badge bg-{{ $usulan->status_class }} ms-2">{{ $usulan->status_label }}</span>
            </h4>
            <div class="d-flex gap-2">
                @if($usulan->status === 'disetujui')
                    <a href="{{ route('usulan.pdf', $usulan->id) }}" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fa fa-file-pdf-o me-1"></i>Download PDF
                    </a>
                    <a href="{{ route('usulan.wa', $usulan->id) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fa fa-whatsapp me-1"></i>Kirim WA
                    </a>
                @endif
                <a href="{{ route('usulan.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>
        <div class="card-body">

            {{-- Info Header --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <table class="table table-borderless table-sm">
                        <tr><th class="text-muted" style="width:160px">Nomor Usulan</th><td>: <strong>{{ $usulan->nomor_usulan }}</strong></td></tr>
                        <tr><th class="text-muted">Tanggal Pengajuan</th><td>: {{ $usulan->tanggal_pengajuan->format('d F Y') }}</td></tr>
                        <tr><th class="text-muted">Unit / Ruangan</th><td>: {{ $usulan->ruangan->nama_ruangan ?? '-' }}</td></tr>
                        <tr><th class="text-muted">Penanggung Jawab</th><td>: {{ $usulan->nama_penanggung_jawab }}</td></tr>
                        <tr><th class="text-muted">Dibuat Oleh</th><td>: {{ $usulan->pembuat->nama ?? '-' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-8">
                    @if($usulan->keterangan)
                        <div class="alert alert-light border">
                            <strong>Keterangan:</strong><br>{{ $usulan->keterangan }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tabel Detail Barang --}}
            <table class="table table-bordered">
                <thead class="table-secondary">
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Keterangan</th>
                        <th style="width:80px" class="text-center">Jumlah</th>
                        <th style="width:150px" class="text-end">Harga Satuan</th>
                        <th style="width:150px" class="text-end">Jumlah (Rp)</th>
                        <th style="width:110px" class="text-center"><i class="fa fa-camera"></i> Foto</th>
                        @if($canApprove)
                        <th style="width:80px" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($usulan->details as $detail)
                    <tr class="{{ $detail->is_ditolak ? 'table-danger' : '' }}">
                        <td class="text-center align-top pt-2">{{ $detail->no_urut }}</td>
                        <td class="align-top pt-2">
                            @if($detail->is_ditolak)
                                <s class="text-danger">{{ $detail->keterangan }}</s>
                                <br><small class="text-danger"><i class="fa fa-times-circle"></i> Ditolak: {{ $detail->alasan_tolak }}</small>
                            @else
                                {{ $detail->keterangan }}
                            @endif
                        </td>
                        <td class="text-center align-top pt-2 {{ $detail->is_ditolak ? 'text-muted' : '' }}">
                            {{ $detail->is_ditolak ? '-' : $detail->jumlah }}
                        </td>
                        <td class="text-end align-top pt-2 {{ $detail->is_ditolak ? 'text-muted' : '' }}">
                            {{ $detail->is_ditolak ? '-' : 'Rp ' . number_format($detail->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold align-top pt-2 {{ $detail->is_ditolak ? 'text-muted' : '' }}">
                            {{ $detail->is_ditolak ? '-' : 'Rp ' . number_format($detail->subtotal, 0, ',', '.') }}
                        </td>
                        {{-- Lampiran per item --}}
                        <td class="text-center align-top" style="padding:6px;">
                            @if($detail->lampirans->count() > 0)
                                <div class="d-flex flex-wrap gap-1 justify-content-center mb-1">
                                    @foreach($detail->lampirans as $lmp)
                                    <div class="position-relative">
                                        <img src="{{ Storage::url($lmp->path) }}"
                                             class="lampiran-thumb"
                                             data-src="{{ Storage::url($lmp->path) }}"
                                             data-nama="{{ $lmp->nama_file }}"
                                             style="width:50px;height:38px;object-fit:cover;border-radius:3px;border:1px solid #ccc;cursor:zoom-in;"
                                             title="Klik untuk perbesar — {{ $lmp->nama_file }}">
                                        @if($canEdit)
                                        <form action="{{ route('usulan.lampiran.delete', [$usulan->id, $lmp->id]) }}"
                                              method="POST"
                                              onsubmit="return confirm('Hapus foto ini?')"
                                              style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-danger p-0 position-absolute top-0 end-0"
                                                    style="width:16px;height:16px;font-size:9px;line-height:1;"
                                                    title="Hapus foto">&times;</button>
                                        </form>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                            @if($canEdit)
                            <form action="{{ route('usulan.lampiran.upload', $usulan->id) }}" method="POST"
                                  enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="detail_id" value="{{ $detail->id }}">
                                <label class="btn btn-outline-secondary p-1 mb-0" title="Tambah foto" style="font-size:11px;cursor:pointer;">
                                    <i class="fa fa-camera"></i>
                                    <input type="file" name="lampirans[]" class="d-none" accept="image/*" multiple
                                           onchange="this.form.submit()">
                                </label>
                            </form>
                            @endif
                        </td>
                        @if($canApprove)
                        <td class="text-center align-top pt-2">
                            @if($detail->is_ditolak)
                                <form action="{{ route('usulan.restore-item', [$usulan->id, $detail->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm" title="Pulihkan item"
                                            onclick="return confirm('Pulihkan item ini?')">
                                        <i class="fa fa-undo"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-danger btn-sm btn-tolak-item"
                                        title="Tolak item"
                                        data-id="{{ $detail->id }}"
                                        data-keterangan="{{ $detail->keterangan }}">
                                    <i class="fa fa-times"></i>
                                </button>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="{{ $canApprove ? 7 : 6 }}" class="text-center text-muted">Belum ada item</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="{{ $canApprove ? 6 : 5 }}" class="text-end">TOTAL</td>
                        <td class="text-end">Rp {{ number_format($usulan->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>

            {{-- Progress Approval --}}
            <div class="card mt-4">
                <div class="card-header bg-light"><strong>Progress Persetujuan</strong></div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $levelInfo = [
                                2 => ['label' => 'Dikonfirmasi Oleh', 'sublabel' => 'Direktur',     'icon' => 'fa-check-circle'],
                                1 => ['label' => 'Diperiksa Oleh',    'sublabel' => 'Kabag/Kabid',  'icon' => 'fa-search'],
                                3 => ['label' => 'Diketahui Oleh',    'sublabel' => 'Ka. Keuangan', 'icon' => 'fa-eye'],
                                4 => ['label' => 'Disetujui Oleh',    'sublabel' => 'Bendahara',    'icon' => 'fa-thumbs-up'],
                            ];
                        @endphp
                        @foreach($levelInfo as $level => $info)
                            @php $approval = $usulan->getApprovalByLevel($level); @endphp
                            <div class="col-md-3 mb-3">
                                <div class="card h-100 {{ $approval ? 'border-success' : 'border-secondary' }}">
                                    <div class="card-body text-center">
                                        <i class="fa {{ $info['icon'] }} fa-2x mb-2 {{ $approval ? 'text-success' : 'text-muted' }}"></i>
                                        <h6 class="card-title mb-0">{{ $info['label'] }}</h6>
                                        <small class="text-muted">({{ $info['sublabel'] }})</small>
                                        @if($approval)
                                            <p class="mb-1 fw-bold text-success">{{ $approval->approver->nama ?? '-' }}</p>
                                            <small class="text-muted">{{ $approval->approved_at->format('d/m/Y H:i') }}</small>
                                            @if($approval->catatan)
                                                <p class="small mt-1 text-muted fst-italic">"{{ $approval->catatan }}"</p>
                                            @endif
                                            <span class="badge bg-success mt-1">Disetujui</span>
                                        @else
                                            @php
                                                $rejectedApproval = $usulan->approvals->where('level', $level)->where('status', 'rejected')->first();
                                            @endphp
                                            @if($rejectedApproval)
                                                <p class="mb-1 fw-bold text-danger">{{ $rejectedApproval->approver->nama ?? '-' }}</p>
                                                <small class="text-muted">{{ $rejectedApproval->approved_at->format('d/m/Y H:i') }}</small>
                                                @if($rejectedApproval->catatan)
                                                    <p class="small mt-1 text-danger fst-italic">"{{ $rejectedApproval->catatan }}"</p>
                                                @endif
                                                <span class="badge bg-danger mt-1">Ditolak</span>
                                            @else
                                                <p class="text-muted small">Menunggu...</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Lampiran Foto --}}
            @if($usulan->lampirans->count() > 0)
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <strong><i class="fa fa-paperclip me-1"></i>Lampiran Foto</strong>
                    <span class="badge bg-secondary ms-1">{{ $usulan->lampirans->count() }}</span>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($usulan->lampirans as $lmp)
                        <div class="text-center" style="width:140px;">
                            <img src="{{ Storage::url($lmp->path) }}" class="img-thumbnail lampiran-thumb"
                                 data-src="{{ Storage::url($lmp->path) }}"
                                 data-nama="{{ $lmp->nama_file }}"
                                 style="width:140px;height:105px;object-fit:cover;cursor:zoom-in;"
                                 title="Klik untuk perbesar — {{ $lmp->nama_file }}">
                            <div class="text-truncate small text-muted mt-1" title="{{ $lmp->nama_file }}">{{ $lmp->nama_file }}</div>
                            @if($canEdit)
                            <form action="{{ route('usulan.lampiran.delete', [$usulan->id, $lmp->id]) }}"
                                  method="POST" class="d-inline mt-1"
                                  onsubmit="return confirm('Hapus lampiran ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Hapus</button>
                            </form>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    @if($canEdit)
                    <hr>
                    <form action="{{ route('usulan.lampiran.upload', $usulan->id) }}" method="POST"
                          enctype="multipart/form-data" class="mt-2">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="lampirans[]" class="form-control" accept="image/*" multiple required>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-upload me-1"></i>Tambah Foto
                            </button>
                        </div>
                        <small class="text-muted">Maks. 5 foto, tiap file maks. 5MB</small>
                    </form>
                    @endif
                </div>
            </div>
            @elseif($canEdit)
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <strong><i class="fa fa-paperclip me-1"></i>Lampiran Foto</strong>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Belum ada lampiran.</p>
                    <form action="{{ route('usulan.lampiran.upload', $usulan->id) }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="lampirans[]" class="form-control" accept="image/*" multiple required>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-upload me-1"></i>Tambah Foto
                            </button>
                        </div>
                        <small class="text-muted">Maks. 5 foto, tiap file maks. 5MB</small>
                    </form>
                </div>
            </div>
            @endif

            {{-- Tombol Aksi --}}
            <div class="d-flex gap-2 mt-3 flex-wrap">
                @if($canSubmit)
                    <form action="{{ route('usulan.submit', $usulan->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Yakin ingin mengajukan usulan ini?')">
                            <i class="fa fa-paper-plane me-1"></i>Ajukan untuk Persetujuan
                        </button>
                    </form>
                @endif

                @if($canEdit)
                    <a href="{{ route('usulan.edit', $usulan->id) }}" class="btn btn-warning">
                        <i class="fa fa-edit me-1"></i>Edit
                    </a>
                    <form action="{{ route('usulan.destroy', $usulan->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Yakin hapus usulan ini?')">
                            <i class="fa fa-trash me-1"></i>Hapus
                        </button>
                    </form>
                @endif

                @if($usulan->status === 'ditolak' && $usulan->dibuat_oleh == auth()->user()->idUser)
                    <form action="{{ route('usulan.resubmit', $usulan->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-info"
                                onclick="return confirm('Ajukan ulang usulan ini?')">
                            <i class="fa fa-refresh me-1"></i>Ajukan Ulang
                        </button>
                    </form>
                @endif

                @if($canApprove)
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalApprove">
                        <i class="fa fa-check me-1"></i>Setujui
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalReject">
                        <i class="fa fa-times me-1"></i>Tolak
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Approve --}}
@if($canApprove)
<div class="modal fade" id="modalApprove" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('usulan.approve', $usulan->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-success"><i class="fa fa-check-circle me-2"></i>Konfirmasi Persetujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda akan menyetujui usulan <strong>{{ $usulan->nomor_usulan }}</strong>.</p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-check me-1"></i>Ya, Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Reject --}}
<div class="modal fade" id="modalReject" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('usulan.reject', $usulan->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="fa fa-times-circle me-2"></i>Konfirmasi Penolakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda akan menolak usulan <strong>{{ $usulan->nomor_usulan }}</strong>.</p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="catatan" class="form-control" rows="3" required
                                  placeholder="Wajib isi alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fa fa-times me-1"></i>Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Tolak Item --}}
@if($canApprove)
<div class="modal fade" id="modalTolakItem" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form-tolak-item" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="fa fa-times-circle me-2"></i>Tolak Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Item: <strong id="label-item-keterangan"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_tolak" class="form-control" rows="3" required
                                  placeholder="Contoh: stok masih ada, tidak sesuai kebutuhan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fa fa-times me-1"></i>Tolak Item Ini</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).on('click', '.btn-tolak-item', function() {
    var id = $(this).data('id');
    var ket = $(this).data('keterangan');
    var url = '{{ route("usulan.reject-item", [$usulan->id, "__ID__"]) }}'.replace('__ID__', id);
    $('#form-tolak-item').attr('action', url);
    $('#form-tolak-item textarea').val('');
    $('#label-item-keterangan').text(ket);
    $('#modalTolakItem').modal('show');
});
</script>
@endpush
@endif

{{-- Modal Lightbox Lampiran --}}
<div class="modal fade" id="modalLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 pb-1 pt-2 px-3">
                <span class="text-white small" id="lightbox-nama"></span>
                <div class="d-flex gap-2 ms-auto">
                    <a id="lightbox-open-link" href="#" target="_blank" class="btn btn-sm btn-outline-light" title="Buka di tab baru">
                        <i class="fa fa-external-link"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-light" data-bs-dismiss="modal" title="Tutup">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body text-center p-2">
                <img id="lightbox-img" src="" alt=""
                     style="max-width:100%;max-height:80vh;object-fit:contain;border-radius:4px;">
            </div>
            {{-- Navigasi prev/next --}}
            <div class="modal-footer border-0 justify-content-center pt-0 pb-2 gap-3">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="lightbox-prev">
                    <i class="fa fa-chevron-left"></i> Sebelumnya
                </button>
                <span class="text-muted small" id="lightbox-counter"></span>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="lightbox-next">
                    Berikutnya <i class="fa fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var allThumbs = [];
    var currentIdx = 0;

    function buildGallery() {
        allThumbs = [];
        $('.lampiran-thumb').each(function() {
            allThumbs.push({
                src: $(this).data('src'),
                nama: $(this).data('nama')
            });
            // simpan index di elemen agar bisa diklik
            $(this).data('idx', allThumbs.length - 1);
        });
    }

    function showLightbox(idx) {
        if (idx < 0) idx = allThumbs.length - 1;
        if (idx >= allThumbs.length) idx = 0;
        currentIdx = idx;
        var item = allThumbs[idx];
        $('#lightbox-img').attr('src', item.src).attr('alt', item.nama);
        $('#lightbox-nama').text(item.nama);
        $('#lightbox-open-link').attr('href', item.src);
        $('#lightbox-counter').text((idx + 1) + ' / ' + allThumbs.length);
        $('#lightbox-prev, #lightbox-next').toggle(allThumbs.length > 1);
    }

    $(document).on('click', '.lampiran-thumb', function(e) {
        e.preventDefault();
        buildGallery();
        showLightbox($(this).data('idx'));
        $('#modalLightbox').modal('show');
    });

    $('#lightbox-prev').on('click', function() { showLightbox(currentIdx - 1); });
    $('#lightbox-next').on('click', function() { showLightbox(currentIdx + 1); });

    // navigasi keyboard
    $('#modalLightbox').on('keydown', function(e) {
        if (e.key === 'ArrowLeft')  showLightbox(currentIdx - 1);
        if (e.key === 'ArrowRight') showLightbox(currentIdx + 1);
    });
})();
</script>
@endpush

@endsection
