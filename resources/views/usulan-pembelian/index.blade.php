@extends('_layouts.app')
@section('title', 'Usulan Pembelian Barang')

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
                <i class="fa fa-file-text me-2"></i>Usulan Pembelian Barang
                @if($pendingCount > 0)
                    <span class="badge bg-danger ms-2">{{ $pendingCount }} menunggu approval Anda</span>
                @endif
            </h4>
            <a href="{{ route('usulan.create') }}" class="btn btn-primary btn-sm" id="btn-buat-usulan" title="Buat Usulan Baru (Alt+N)">
                <i class="fa fa-plus me-1"></i>Buat Usulan
                <kbd class="ms-1" style="font-size:10px;opacity:.75;background:rgba(255,255,255,.2);border:none;color:#fff;">Alt+N</kbd>
            </a>
        </div>
        <div class="card-body">
            <table id="usulan-table" class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nomor Usulan</th>
                        <th>Tanggal</th>
                        <th>Unit/Ruangan</th>
                        <th>Penanggung Jawab</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Konfirmasi sebelum ajukan
    $(document).on('submit', '.form-ajukan', function(e) {
        e.preventDefault();
        var nomor = $(this).data('nomor');
        if (confirm('Ajukan usulan ' + nomor + '?\nSetelah diajukan, usulan tidak dapat diedit.')) {
            this.submit();
        }
    });

    // Shortcut Alt+N → buka form buat usulan baru
    $(document).on('keydown', function(e) {
        if (e.altKey && e.key === 'n') {
            e.preventDefault();
            window.location.href = $('#btn-buat-usulan').attr('href');
        }
    });

    var table = $('#usulan-table').DataTable({
        ajax: {
            url: '{{ route("usulan.data") }}',
            dataSrc: function(json) {
                // Update badge pending count
                var badge = $('h4 .badge.bg-danger');
                if (json.pendingCount > 0) {
                    if (badge.length) badge.text(json.pendingCount + ' menunggu approval Anda');
                    else $('h4.mb-0').append('<span class="badge bg-danger ms-2">' + json.pendingCount + ' menunggu approval Anda</span>');
                } else {
                    badge.remove();
                }
                return json.rows;
            }
        },
        columns: [
            { data: null, render: function(data, type, row, meta) { return meta.row + 1; }, orderable: false },
            { data: 'nomor_usulan', render: function(d) { return '<strong>' + d + '</strong>'; } },
            { data: 'tanggal', orderData: 2, render: function(d, t, row) {
                return t === 'sort' ? row.tanggal_order : d;
            }},
            { data: 'ruangan' },
            { data: 'penanggung_jawab' },
            { data: 'status_label', render: function(d, t, row) {
                return '<span class="badge bg-' + row.status_class + '">' + d + '</span>';
            }},
            { data: 'pembuat' },
            { data: 'aksi', orderable: false, className: 'text-center' },
        ],
        paging: true,
        searching: true,
        ordering: true,
        pageLength: 15,
        order: [[2, 'desc']],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: { first: "Pertama", last: "Terakhir", next: "›", previous: "‹" },
            emptyTable: "Tidak ada data",
            loadingRecords: "Memuat data..."
        }
    });

    // Auto-reload setiap 20 detik
    setInterval(function() {
        table.ajax.reload(null, false); // false = jangan reset ke halaman 1
    }, 20000);
});
</script>
@endpush
