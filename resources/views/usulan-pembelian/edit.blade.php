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
                                    <th style="width:130px" class="text-center"><i class="fa fa-camera"></i> Foto</th>
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
                                    <td class="align-top" style="padding:6px;">
                                        {{-- Foto yang sudah tersimpan --}}
                                        @if($detail->lampirans->count() > 0)
                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                            @foreach($detail->lampirans as $lmp)
                                            <img src="{{ Storage::url($lmp->path) }}"
                                                 style="width:50px;height:38px;object-fit:cover;border-radius:3px;border:1px solid #ccc;"
                                                 title="{{ $lmp->nama_file }}">
                                            @endforeach
                                        </div>
                                        @endif
                                        {{-- Preview foto baru --}}
                                        <div class="item-foto-preview d-flex flex-wrap gap-1 mb-1"></div>
                                        <label class="btn btn-outline-secondary btn-sm mb-0" title="Tambah foto item">
                                            <i class="fa fa-camera"></i>
                                            <input type="file" name="item_lampirans[{{ $i }}][]"
                                                   class="item-lampiran-input d-none" accept="image/*" multiple>
                                        </label>
                                    </td>
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
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <strong><i class="fa fa-paperclip me-1"></i>Lampiran Foto</strong>
                            <small class="text-muted ms-2">Maks. 5 foto, tiap file maks. 5MB (JPG/PNG/GIF/WEBP)</small>
                        </div>
                        <span id="lampiran-counter" class="badge bg-secondary">{{ $usulan->lampirans->count() }} / 5</span>
                    </div>
                    <div class="card-body">
                        {{-- Lampiran yang sudah tersimpan --}}
                        @if($usulan->lampirans->count() > 0)
                            <p class="text-muted small mb-2">Lampiran tersimpan (klik <i class="fa fa-times"></i> untuk hapus):</p>
                            <div class="d-flex flex-wrap gap-2 mb-3" id="existing-lampirans">
                                @foreach($usulan->lampirans as $lmp)
                                <div class="position-relative existing-lampiran-item" style="width:120px;">
                                    <a href="{{ Storage::url($lmp->path) }}" target="_blank">
                                        <img src="{{ Storage::url($lmp->path) }}" class="img-thumbnail"
                                             style="width:120px;height:90px;object-fit:cover;">
                                    </a>
                                    <div class="text-truncate small text-muted mt-1" style="max-width:120px" title="{{ $lmp->nama_file }}">{{ $lmp->nama_file }}</div>
                                    <form action="{{ route('usulan.lampiran.delete', [$usulan->id, $lmp->id]) }}"
                                          method="POST" class="lampiran-delete-form"
                                          onsubmit="return confirm('Hapus lampiran ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0" style="width:22px;height:22px;font-size:10px;line-height:1;">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Gallery upload baru --}}
                        <p class="text-muted small mb-2">Tambah lampiran baru:</p>
                        <div id="lampiran-gallery" class="d-flex flex-wrap gap-2 align-items-start"></div>
                        <input type="file" name="lampirans[]" id="input-lampiran" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
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
                <td class="align-top" style="padding:6px;">
                    <div class="item-foto-preview d-flex flex-wrap gap-1 mb-1"></div>
                    <label class="btn btn-outline-secondary btn-sm mb-0" title="Tambah foto item">
                        <i class="fa fa-camera"></i>
                        <input type="file" name="item_lampirans[${rowIndex}][]" class="item-lampiran-input d-none" accept="image/*" multiple>
                    </label>
                </td>
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

    // Preview foto per item (thumbnail seperti halaman detail)
    $(document).on('change', '.item-lampiran-input', function() {
        const previewBox = $(this).closest('td').find('.item-foto-preview');
        previewBox.empty();
        Array.from(this.files).forEach(function(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewBox.append(
                    `<div class="position-relative">
                        <img src="${e.target.result}"
                             style="width:50px;height:38px;object-fit:cover;border-radius:3px;border:1px solid #ccc;"
                             title="${file.name}">
                    </div>`
                );
            };
            reader.readAsDataURL(file);
        });
    });

    // === Multi-image lampiran gallery ===
    let lampiranItems = [];
    const MAX_LAMPIRAN = 5;
    const existingCount = {{ $usulan->lampirans->count() }};

    function getAvailableSlots() {
        const currentExisting = $('#existing-lampirans .existing-lampiran-item').length;
        return MAX_LAMPIRAN - currentExisting - lampiranItems.length;
    }

    function updateCounter() {
        const currentExisting = $('#existing-lampirans .existing-lampiran-item').length;
        $('#lampiran-counter').text(`${currentExisting + lampiranItems.length} / ${MAX_LAMPIRAN}`);
    }

    function renderLampiranGallery() {
        const gallery = $('#lampiran-gallery');
        gallery.empty();

        lampiranItems.forEach(function(item, idx) {
            gallery.append(`
                <div class="lampiran-thumb position-relative" style="width:120px;">
                    <img src="${item.dataUrl}" class="img-thumbnail"
                         style="width:120px;height:90px;object-fit:cover;cursor:pointer;"
                         onclick="window.open('${item.dataUrl}','_blank')">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0 lampiran-remove-btn"
                            style="width:22px;height:22px;font-size:10px;line-height:1;" data-idx="${idx}">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="text-truncate small text-muted mt-1" style="max-width:120px" title="${item.file.name}">${item.file.name}</div>
                </div>
            `);
        });

        if (getAvailableSlots() > 0) {
            gallery.append(`
                <div id="lampiran-add-btn" onclick="document.getElementById('input-lampiran').click()"
                     style="width:120px;height:90px;border:2px dashed #adb5bd;border-radius:6px;cursor:pointer;color:#6c757d;display:flex;align-items:center;justify-content:center;">
                    <div class="text-center">
                        <i class="fa fa-plus fa-lg"></i>
                        <div class="small mt-1">Tambah Foto</div>
                    </div>
                </div>
            `);
        }

        updateCounter();
        const dt = new DataTransfer();
        lampiranItems.forEach(item => dt.items.add(item.file));
        document.getElementById('input-lampiran').files = dt.files;
    }

    $('#input-lampiran').on('change', function() {
        const newFiles = Array.from(this.files);
        const remaining = getAvailableSlots();
        if (newFiles.length > remaining) {
            alert(`Hanya bisa menambahkan ${remaining} foto lagi (maks. ${MAX_LAMPIRAN}).`);
        }
        const toProcess = newFiles.slice(0, remaining);
        let processed = 0;
        if (toProcess.length === 0) { this.value = ''; return; }

        toProcess.forEach(function(file) {
            if (file.size > 5 * 1024 * 1024) {
                alert(`File "${file.name}" melebihi 5MB dan dilewati.`);
                processed++;
                if (processed === toProcess.length) renderLampiranGallery();
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                lampiranItems.push({ file: file, dataUrl: e.target.result });
                processed++;
                if (processed === toProcess.length) renderLampiranGallery();
            };
            reader.readAsDataURL(file);
        });
        this.value = '';
    });

    $(document).on('click', '.lampiran-remove-btn', function() {
        lampiranItems.splice(parseInt($(this).data('idx')), 1);
        renderLampiranGallery();
    });

    // Update counter when existing lampiran is deleted via AJAX-like submit
    $(document).on('submit', '.lampiran-delete-form', function() {
        $(this).closest('.existing-lampiran-item').remove();
        updateCounter();
    });

    renderLampiranGallery();
});
</script>
@endpush
