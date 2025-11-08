@extends('_layouts.app')

@section('title', 'Master Data - Shift')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i> Data Shift
                    </h4>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createShiftModal">
                        <i class="fas fa-plus me-1"></i> Tambah Shift
                    </button>

             
                </div>
                <div class="card-body">
                    <!-- TAMBAHKAN INI UNTUK DEBUG -->
                    <div id="debug-info" class="alert alert-info d-none">
                        <strong>Debug Info:</strong>
                        <div id="debug-content"></div>
                    </div>
                    
                    <table id="shifts-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Shift</th>
                                <th>Jam Operasional</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shifts as $shift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $shift->name }}</td>
                                <td>{{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-info btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editShiftModal"
                                                data-id="{{ $shift->id }}"
                                                data-name="{{ $shift->name }}"
                                                data-start="{{ $shift->start_time->format('H:i') }}"
                                                data-end="{{ $shift->end_time->format('H:i') }}"
                                                title="Lihat">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <form action="{{ route('shift.destroy', $shift->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirmDelete(event, '{{ $shift->name }}')"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Edit Shift -->
<div class="modal fade" id="editShiftModal" tabindex="-1" aria-labelledby="editShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editShiftForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editShiftModalLabel">Edit Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        
                        <label for="edit_name" class="form-label">Nama Shift</label>
                        <input type="text" class="form-control" id="edit_id" name="id" required>
						<br>
						 <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_start_time" class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" id="edit_start_time" name="start_time" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_end_time" class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control" id="edit_end_time" name="end_time" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>




       <!-- Modal Create Shift -->
                    <div class="modal fade" id="createShiftModal" tabindex="-1" aria-labelledby="createShiftModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('shift.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createShiftModalLabel">Tambah Shift Baru</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nama Shift</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="start_time" class="form-label">Jam Mulai</label>
                                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="end_time" class="form-label">Jam Selesai</label>
                                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>





@endsection

@push('scripts')
<script>
$(document).ready(function() {
    console.log('✅ Document ready - Initializing DataTables...');
    
    // Debug: Cek apakah jQuery terload
    if (typeof $ === 'undefined') {
        console.error('❌ jQuery tidak terload!');
        $('#debug-info').removeClass('d-none');
        $('#debug-content').html('jQuery tidak terload!');
        return;
    }
    
    // Debug: Cek apakah DataTables terload
    if (typeof $.fn.dataTable === 'undefined') {
        console.error('❌ DataTables tidak terload!');
        $('#debug-info').removeClass('d-none');
        $('#debug-content').html('DataTables library tidak terload!');
        return;
    }
    
    // Debug: Cek apakah table exist
    if ($('#shifts-table').length === 0) {
        console.error('❌ Table #shifts-table tidak ditemukan!');
        $('#debug-info').removeClass('d-none');
        $('#debug-content').html('Table dengan ID shifts-table tidak ditemukan!');
        return;
    }
    
    // CONFIGURASI PALING SIMPLE - HANYA FITUR DASAR
    try {
        var table = $('#shifts-table').DataTable({
            // Fitur dasar
            paging: true,        // Pagination
            searching: true,     // Search box
            ordering: true,      // Sorting
            info: true,          // Show info
            responsive: true,    // Responsive
            autoWidth: false,    // Auto width
            
            // Basic configuration
            pageLength: 10,       // Cuma 5 data per halaman untuk testing
            lengthChange: true,  // Show length change dropdown
            lengthMenu: [5, 10, 25, 50], // Length menu options
            
            // Simple language
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir", 
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            },
            
            // Simple DOM layout
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
        });
        
       
        
        // Test: Cek apakah pagination ada
        setTimeout(function() {
            var paginationElement = $('.dataTables_paginate');
            if (paginationElement.length > 0) {
                console.log('✅ Pagination element ditemukan');
            } else {
                console.error('❌ Pagination element tidak ditemukan');
            }
        }, 1000);
        
    } catch (error) {
        console.error('❌ Error inisialisasi DataTables:', error);
        $('#debug-info').removeClass('d-none');
        $('#debug-content').html('Error: ' + error.message);
    }

});

// Fungsi konfirmasi
function confirmDelete(event, shiftName) {
    event.preventDefault();
    const form = event.target.closest('form');
    
    Swal.fire({
        title: 'Hapus Shift?',
        text: `Apakah Anda yakin ingin menghapus shift "${shiftName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Kirim form secara otomatis ke controller
            form.submit();
            
            // Alternatif: Kirim dengan AJAX jika ingin tanpa reload halaman
            // fetch(form.action, {
            //     method: 'POST',
            //     body: new FormData(form),
            //     headers: {
            //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            //     }
            // }).then(response => {
            //     if (response.ok) {
            //         Swal.fire('Terhapus!', 'Shift berhasil dihapus.', 'success').then(() => {
            //             location.reload(); // Reload halaman setelah sukses
            //         });
            //     } else {
            //         Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus shift.', 'error');
            //     }
            // });
        }
    });
}




// Handle modal edit
$('#editShiftModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget); // tombol yang diklik
    
	const id = button.data('id');

    const name = button.data('name');
    const start = button.data('start');
    const end = button.data('end');

    // Set data ke input form
    const modal = $(this);
    modal.find('#edit_id').val(id);
	modal.find('#edit_name').val(name);
    modal.find('#edit_start_time').val(start);
    modal.find('#edit_end_time').val(end);

    // Ganti action form supaya mengarah ke route update
    const updateUrl = `/shift/${id}`; // pastikan route update kamu: Route::put('/shift/{id}', ...)
    modal.find('#editShiftForm').attr('action', updateUrl);
});


</script>
@endpush