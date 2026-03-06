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
                        @if($canApprove)
                        <th style="width:80px" class="text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($usulan->details as $detail)
                    <tr class="{{ $detail->is_ditolak ? 'table-danger' : '' }}">
                        <td class="text-center">{{ $detail->no_urut }}</td>
                        <td>
                            @if($detail->is_ditolak)
                                <s class="text-danger">{{ $detail->keterangan }}</s>
                                <br><small class="text-danger"><i class="fa fa-times-circle"></i> Ditolak: {{ $detail->alasan_tolak }}</small>
                            @else
                                {{ $detail->keterangan }}
                            @endif
                        </td>
                        <td class="text-center {{ $detail->is_ditolak ? 'text-muted' : '' }}">
                            {{ $detail->is_ditolak ? '-' : $detail->jumlah }}
                        </td>
                        <td class="text-end {{ $detail->is_ditolak ? 'text-muted' : '' }}">
                            {{ $detail->is_ditolak ? '-' : 'Rp ' . number_format($detail->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="text-end fw-bold {{ $detail->is_ditolak ? 'text-muted' : '' }}">
                            {{ $detail->is_ditolak ? '-' : 'Rp ' . number_format($detail->subtotal, 0, ',', '.') }}
                        </td>
                        @if($canApprove)
                        <td class="text-center">
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
                    <tr><td colspan="{{ $canApprove ? 6 : 5 }}" class="text-center text-muted">Belum ada item</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="{{ $canApprove ? 5 : 4 }}" class="text-end">TOTAL</td>
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

@endsection
