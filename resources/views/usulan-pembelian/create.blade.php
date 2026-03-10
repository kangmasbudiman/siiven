@extends('_layouts.app')
@section('title', 'Buat Usulan Pembelian')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fa fa-plus me-2"></i>Buat Usulan Pembelian Barang</h4>
            <a href="{{ route('usulan.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i>Kembali
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

            <form action="{{ route('usulan.store') }}" method="POST" id="form-usulan" enctype="multipart/form-data">
                @csrf

                {{-- Info Pengaju (otomatis dari profil login) --}}
                <div class="card mb-3 border-primary">
                    <div class="card-header bg-primary text-white py-2">
                        <strong><i class="fa fa-user me-1"></i>Data Pengaju (Otomatis dari Akun Anda)</strong>
                    </div>
                    <div class="card-body py-3">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Dibuat Oleh</small>
                                <div class="form-control bg-light">{{ $user->nama }}</div>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Unit / Ruangan</small>
                                @if($userRuangan)
                                    <div class="form-control bg-light">{{ $userRuangan->nama_ruangan }}</div>
                                @elseif($ruangans)
                                    {{-- Hanya Admin yang bisa pilih ruangan --}}
                                    <select name="ruangan_id" class="form-select" required>
                                        <option value="">-- Pilih Unit/Ruangan --</option>
                                        @foreach($ruangans as $r)
                                            <option value="{{ $r->id }}" {{ old('ruangan_id') == $r->id ? 'selected' : '' }}>
                                                {{ $r->nama_ruangan }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <div class="alert alert-warning mb-0 py-2">
                                        <i class="fa fa-warning me-1"></i>Akun Anda belum memiliki ruangan. Hubungi Admin.
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block mb-1">Tanggal Pengajuan <span class="text-danger">*</span></small>
                                <input type="date" name="tanggal_pengajuan" class="form-control"
                                       value="{{ old('tanggal_pengajuan', date('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tabel Items --}}
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
                                @php $oldItems = old('items', array_fill(0, 5, ['keterangan'=>'','jumlah'=>1,'harga_satuan'=>0])); @endphp
                                @foreach($oldItems as $i => $item)
                                <tr class="item-row">
                                    <td class="text-center align-middle row-number">{{ $i + 1 }}</td>
                                    <td>
                                        <input type="text" name="items[{{ $i }}][keterangan]"
                                               class="form-control form-control-sm"
                                               value="{{ $item['keterangan'] ?? '' }}"
                                               placeholder="Nama / keterangan barang">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][jumlah]"
                                               class="form-control form-control-sm jumlah-input" min="1"
                                               value="{{ $item['jumlah'] ?? 1 }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][harga_satuan]"
                                               class="form-control form-control-sm harga-input" min="0"
                                               value="{{ $item['harga_satuan'] ?? 0 }}">
                                    </td>
                                    <td class="text-end align-middle subtotal-cell fw-bold">Rp 0</td>
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
                    <textarea name="keterangan" class="form-control" rows="3"
                              placeholder="Tuliskan alasan / keterangan pengajuan...">{{ old('keterangan') }}</textarea>
                </div>

                {{-- Lampiran Foto --}}
                <div class="card mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <strong><i class="fa fa-paperclip me-1"></i>Lampiran Foto (Opsional)</strong>
                            <small class="text-muted ms-2">Maks. 5 foto, tiap file maks. 5MB (JPG/PNG/GIF/WEBP)</small>
                        </div>
                        <span id="lampiran-counter" class="badge bg-secondary">0 / 5</span>
                    </div>
                    <div class="card-body">
                        <div id="lampiran-gallery" class="d-flex flex-wrap gap-2 align-items-start"></div>
                        <input type="file" name="lampirans[]" id="input-lampiran" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i>Simpan sebagai Draft
                    </button>
                    <a href="{{ route('usulan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let rowIndex = {{ count(old('items', array_fill(0, 5, []))) }};

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
                <td><input type="text" name="items[${rowIndex}][keterangan]" class="form-control form-control-sm" placeholder="Nama / keterangan barang"></td>
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
        if ($('.item-row').length <= 1) { alert('Minimal harus ada 1 item.'); return; }
        $(this).closest('tr').remove();
        updateRowNumbers();
        hitungTotal();
    });

    // === Multi-image lampiran gallery ===
    let lampiranItems = [];
    const MAX_LAMPIRAN = 5;

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

        if (lampiranItems.length < MAX_LAMPIRAN) {
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

        $('#lampiran-counter').text(`${lampiranItems.length} / ${MAX_LAMPIRAN}`);

        const dt = new DataTransfer();
        lampiranItems.forEach(item => dt.items.add(item.file));
        document.getElementById('input-lampiran').files = dt.files;
    }

    $('#input-lampiran').on('change', function() {
        const newFiles = Array.from(this.files);
        const remaining = MAX_LAMPIRAN - lampiranItems.length;
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

    renderLampiranGallery();
});
</script>
@endpush
