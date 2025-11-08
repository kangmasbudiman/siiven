@extends('_layouts.app')

@section('title', 'Management User - Shift')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i> Management User - Shift
                    </h4>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignUserShiftModal">
                        <i class="fas fa-plus me-1"></i> Assign Shift
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
                                <th>Nama Admin</th>
                                <th>Username</th>
                                <th>Shift</th>
                                <th>Jam Shift</th>
                                <th>Assigned By</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userShifts as $userShift)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $userShift->user->nama }}</td>
                                <td>{{ $userShift->user->username }}</td>
                                <td>{{ $userShift->shift->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($userShift->shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($userShift->shift->end_time)->format('H:i') }}</td>
                                <td>{{ $userShift->assignedBy->nama }}</td>
                                <td>
                                    @if($userShift->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" 
                                                class="btn btn-info btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editUserShiftModal"
                                                data-id="{{ $userShift->id }}"
                                                data-user-id="{{ $userShift->user_id }}"
                                                data-shift-id="{{ $userShift->shift_id }}"
                                                title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <form action="{{ route('usershift.toggleStatus', $userShift->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-warning btn-sm" 
                                                    onclick="return confirmToggle(event, '{{ $userShift->user->nama }}')"
                                                    title="{{ $userShift->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas {{ $userShift->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('usershift.destroy', $userShift->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                    onclick="return confirmDelete(event, '{{ $userShift->user->nama }}')"
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

<!-- Modal Assign User Shift -->
<div class="modal fade" id="assignUserShiftModal" tabindex="-1" aria-labelledby="assignUserShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('usershift.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assignUserShiftModalLabel">Assign Shift ke User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pilih Admin</label>
                        <select class="form-control" id="user_id" name="user_id" required>
                            <option value="">-- Pilih Admin --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->idUser }}">
                                    {{ $user->nama }} ({{ $user->username }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="shift_id" class="form-label">Pilih Shift</label>
                        <select class="form-control" id="shift_id" name="shift_id" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>


                      <div class="mb-3">
                        <label for="edit_is_active" class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Assign Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit User Shift -->
<div class="modal fade" id="editUserShiftModal" tabindex="-1" aria-labelledby="editUserShiftModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserShiftForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserShiftModalLabel">Edit User Shift</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label for="edit_user_id" class="form-label">Pilih Admin</label>
                        <select class="form-control" id="edit_user_id" name="user_id" required>
                            <option value="">-- Pilih Admin --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->idUser }}">
                                    {{ $user->nama }} ({{ $user->username }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_shift_id" class="form-label">Pilih Shift</label>
                        <select class="form-control" id="edit_shift_id" name="shift_id" required>
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $shift)
                                <option value="{{ $shift->id }}">
                                    {{ $shift->name }} ({{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                        <div class="mb-3">
                        <label for="edit_is_active" class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1" {{ $userShift->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="edit_is_active">Active</label>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Shift</button>
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

// Fungsi konfirmasi hapus
function confirmDelete(event, userName) {
    event.preventDefault();
    const form = event.target.closest('form');
    
    Swal.fire({
        title: 'Hapus Assign Shift?',
        text: `Apakah Anda yakin ingin menghapus assign shift untuk user "${userName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

// Fungsi konfirmasi toggle aktif/nonaktif
function confirmToggle(event, userName) {
    event.preventDefault();
    const form = event.target.closest('form');
    const isActive = form.querySelector('button').title.includes('Nonaktifkan');
    
    Swal.fire({
        title: isActive ? 'Nonaktifkan Shift?' : 'Aktifkan Shift?',
        text: `Apakah Anda yakin ingin ${isActive ? 'menonaktifkan' : 'mengaktifkan'} shift untuk user "${userName}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: isActive ? '#f39c12' : '#28a745',
        cancelButtonColor: '#3085d6',
        confirmButtonText: isActive ? 'Ya, Nonaktifkan!' : 'Ya, Aktifkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

// Handle modal edit user shift
$('#editUserShiftModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const id = button.data('id');
    const userId = button.data('user-id');
    const shiftId = button.data('shift-id');

    // Set data ke input form
    const modal = $(this);
    modal.find('#edit_id').val(id);
    modal.find('#edit_user_id').val(userId);
    modal.find('#edit_shift_id').val(shiftId);

    // Ganti action form supaya mengarah ke route update user-shift
    const updateUrl = `/usershift/${id}`;
    modal.find('#editUserShiftForm').attr('action', updateUrl);
});

// Reset form ketika modal ditutup
$('#assignUserShiftModal').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
});

$('#editUserShiftModal').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
});
</script>
@endpush