@extends('_layouts.app')
@section('title', 'Edit Usulan - ' . $usulan->nomor_usulan)

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fa fa-edit me-2"></i>Edit Usulan {{ $usulan->nomor_usulan }}</h4>
            <a href="{{ route('usulan.show', $usulan->id) }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('usulan.update', $usulan->id) }}" method="POST" id="form-usulan" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Info Pengaju (hanya baca) --}}
                <div class="card mb-3 border-primary">
                    <div class="card-header bg-primary text-white py-2">
                        <strong><i class="fa fa-user me-1"></i>Data Pengaju</strong>
                    </div>
                    <div class="card-body py-3">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Dibuat Oleh</small>
                                <div class="form-control bg-light">{{ $user->nama }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Unit / Ruangan</small>
                                @if($ruangans)
                                    {{-- Admin bisa ubah ruangan --}}
                                    <select name="ruangan_id" class="form-select" required>
                                        <option value="">-- Pilih Ruangan --</option>
                                        @foreach($ruangans as $r)
                                            <option value="{{ $r->id }}" {{ $usulan->ruangan_id == $r->id ? 'selected' : '' }}>
                                                {{ $r->nama_ruangan }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="form-control bg-light">{{ $usulan->ruangan->nama_ruangan ?? '-' }}</div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Tanggal Pengajuan <span class="text-danger">*</span></small>
                                <input type="date" name="tanggal_pengajuan" class="form-control"
                                       value="{{ old('tanggal_pengajuan', $usulan->tanggal_pengajuan->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <strong>Daftar Barang yang Diusulkan</strong>
                        <button type="button" class="btn btn-success btn-sm" id="btn-add-row">
                            <i class="fa fa-plus me-1"></i>Tambah Baris
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead class="table-secondary">
                                <tr>
                                    <th style="width:40px">No</th>
                                    <th>Keterangan / Nama Barang</th>
                                    <th style="width:80px">Jumlah</th>
                                    <th style="width:140px">Harga Satuan (Rp)</th>
                                    <th style="width:140px">Jumlah (Rp)</th>
                                    <th style="width:40px"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body">
                                @foreach($usulan->details as $i => $detail)
                                <tr class="item-row">
                                    <td class="text-center align-middle row-number">{{ $i + 1 }}</td>
                                    <td>
                                        <input type="hidden" name="items[{{ $i }}][id]" value="{{ $detail->id }}">
                                        <input type="text" name="items[{{ $i }}][keterangan]"
                                               class="form-control form-control-sm"
                                               value="{{ $detail->keterangan }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][jumlah]"
                                               class="form-control form-control-sm jumlah-input" min="1"
                                               value="{{ $detail->jumlah }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][harga_satuan]"
                                               class="form-control form-control-sm harga-input" min="0"
                                               value="{{ $detail->harga_satuan }}">
                                    </td>
                                    <td class="text-end align-middle subtotal-cell fw-bold"></td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-row">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td colspan="4" class="text-end">TOTAL</td>
                                    <td class="text-end" id="grand-total">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan Tambahan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $usulan->keterangan) }}</textarea>
                </div>

                {{-- Lampiran Foto --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong><i class="fa fa-paperclip me-1"></i>Lampiran Foto</strong>
                        <small class="text-muted ms-2">Maks. 5 foto, tiap file maks. 5MB (JPG/PNG/GIF/WEBP)</small>
                    </div>
                    <div class="card-body">
                        {{-- Lampiran yang sudah ada --}}
                        @if($usulan->lampirans->count() > 0)
                            <p class="text-muted small mb-2">Lampiran saat ini (klik <i class="fa fa-trash"></i> untuk hapus):</p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach($usulan->lampirans as $lmp)
                                <div class="position-relative" style="width:120px;">
                                    <a href="{{ Storage::url($lmp->path) }}" target="_blank">
                                        <img src="{{ Storage::url($lmp->path) }}" class="img-thumbnail"
                                             style="width:120px;height:90px;object-fit:cover;">
                                    </a>
                                    <div class="text-truncate small text-muted mt-1" style="max-width:120px" title="{{ $lmp->nama_file }}">{{ $lmp->nama_file }}</div>
                                    <form action="{{ route('usulan.lampiran.delete', [$usulan->id, $lmp->id]) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus lampiran ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0" style="width:22px;height:22px;font-size:10px;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Upload lampiran baru --}}
                        <label class="form-label small">Tambah Lampiran Baru:</label>
                        <input type="file" name="lampirans[]" id="input-lampiran" class="form-control"
                               accept="image/*" multiple>
                        <div id="preview-lampiran" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('usulan.show', $usulan->id) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let rowIndex = {{ $usulan->details->count() }};

function formatRupiah(angka) {
    return 'Rp ' + parseInt(angka || 0).toLocaleString('id-ID');
}

function hitungSubtotal(row) {
    const jumlah = parseInt($(row).find('.jumlah-input').val()) || 0;
    const harga  = parseInt($(row).find('.harga-input').val()) || 0;
    $(row).find('.subtotal-cell').text(formatRupiah(jumlah * harga));
    return jumlah * harga;
}

function hitungTotal() {
    let total = 0;
    $('.item-row').each(function() { total += hitungSubtotal(this); });
    $('#grand-total').text(formatRupiah(total));
}

function updateRowNumbers() {
    $('.item-row').each(function(i) {
        $(this).find('.row-number').text(i + 1);
        $(this).find('input').each(function() {
            const name = $(this).attr('name');
            if (name) $(this).attr('name', name.replace(/items\[\d+\]/, `items[${i}]`));
        });
    });
}

$(document).ready(function() {
    hitungTotal();

    $('#btn-add-row').on('click', function() {
        const html = `
            <tr class="item-row">
                <td class="text-center align-middle row-number">${$('.item-row').length + 1}</td>
                <td>
                    <input type="hidden" name="items[${rowIndex}][id]" value="">
                    <input type="text" name="items[${rowIndex}][keterangan]" class="form-control form-control-sm" placeholder="Nama / keterangan barang">
                </td>
                <td><input type="number" name="items[${rowIndex}][jumlah]" class="form-control form-control-sm jumlah-input" min="1" value="1"></td>
                <td><input type="number" name="items[${rowIndex}][harga_satuan]" class="form-control form-control-sm harga-input" min="0" value="0"></td>
                <td class="text-end align-middle subtotal-cell fw-bold">Rp 0</td>
                <td class="text-center align-middle"><button type="button" class="btn btn-danger btn-sm btn-remove-row"><i class="fa fa-trash"></i></button></td>
            </tr>`;
        $('#items-body').append(html);
        rowIndex++;
    });

    $(document).on('input', '.jumlah-input, .harga-input', function() { hitungTotal(); });

    $(document).on('click', '.btn-remove-row', function() {
        if ($('.item-row').length <= 1) { alert('Minimal 1 item.'); return; }
        $(this).closest('tr').remove();
        updateRowNumbers();
        hitungTotal();
    });

    // Preview lampiran umum
    $('#input-lampiran').on('change', function() {
        const preview = $('#preview-lampiran');
        preview.empty();
        const files = this.files;
        if (files.length > 5) {
            alert('Maksimal 5 foto per unggahan.');
            this.value = '';
            return;
        }
        Array.from(files).forEach(function(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.append(`<div class="position-relative" style="width:120px;">
                    <img src="${e.target.result}" class="img-thumbnail" style="width:120px;height:90px;object-fit:cover;">
                    <div class="text-truncate small text-muted mt-1" style="max-width:120px">${file.name}</div>
                </div>`);
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>
@endpush
