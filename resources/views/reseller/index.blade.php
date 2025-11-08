@extends('_layouts.app')

@section('title', 'Master Data - Reseller')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Data Reseller
                    </h4>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createResellerModal">
                        <i class="fas fa-plus me-1"></i>Tambah Reseller
                    </button>
                </div>
                <div class="card-body">
                    <table id="resellers-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Reseller</th>
                                <th>Kontak</th>
                                <th>Saldo Hutang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resellers as $reseller)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $reseller->code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $reseller->namereseller }}</strong>
                                    @if($reseller->notes)
                                        <br><small class="text-muted">{{ Str::limit($reseller->notes, 30) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($reseller->phone)
                                        <div><i class="fas fa-phone me-1"></i> {{ $reseller->phone }}</div>
                                    @endif
                                    @if($reseller->email)
                                        <div><i class="fas fa-envelope me-1"></i> {{ $reseller->email }}</div>
                                    @endif
                                </td>
                                <td class="text-end {{ $reseller->initial_balance > 0 ? 'text-danger' : 'text-success' }}">
                                    <strong>{{ $reseller->getFormattedBalanceAttribute() }}</strong>
                                </td>
                                <td class="text-center">
                                    @if($reseller->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editResellerModal" 
                                            data-id="{{ $reseller->id }}"
                                            data-name="{{ $reseller->name }}"
                                            data-code="{{ $reseller->code }}"
                                            data-phone="{{ $reseller->phone }}"
                                            data-email="{{ $reseller->email }}"
                                            data-address="{{ $reseller->address }}"
                                            data-initial_balance="{{ $reseller->initial_balance }}"
                                            data-notes="{{ $reseller->notes }}"
                                            data-is_active="{{ $reseller->is_active ? '1' : '0' }}"
                                          
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <form action="{{ route('reseller.toggle-status', $reseller->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn {{ $reseller->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                    onclick="return confirmToggleStatus(event, '{{ $reseller->name }}', {{ $reseller->is_active }})"
                                                    title="{{ $reseller->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas {{ $reseller->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        @if($reseller->canDelete())
                                        <form action="{{ route('reseller.destroy', $reseller->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirmDelete(event, '{{ $reseller->name }}')"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
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

<!-- Modals -->
@include('reseller.modals')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi DataTables
    $('#resellers-table').DataTable({
        responsive: true,
        autoWidth: false,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        columnDefs: [
            { orderable: false, targets: [0, 6] },
            { searchable: false, targets: [0, 1, 4, 5, 6] }
        ],
        order: [[2, 'asc']],
        pageLength: 10
    });

    // Handle edit modal
    $('#editResellerModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var code = button.data('code');
        var phone = button.data('phone');
        var email = button.data('email');
        var address = button.data('address');
        var initial_balance = button.data('initial_balance');
        var notes = button.data('notes');
        var is_active = button.data('is_active');
        var applications = button.data('applications');

        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_name').val(name);
        modal.find('#edit_code').val(code);
        modal.find('#edit_phone').val(phone);
        modal.find('#edit_email').val(email);
        modal.find('#edit_address').val(address);
        modal.find('#edit_initial_balance').val(initial_balance);
        modal.find('#edit_notes').val(notes);
        modal.find('#edit_is_active').prop('checked', is_active == '1');

        // Set special prices for applications
        if (applications) {
            Object.keys(applications).forEach(function(appId) {
                var priceInput = modal.find('#special_price_' + appId);
                if (priceInput.length) {
                    priceInput.val(applications[appId]);
                }
            });
        }

        // Update form action
        modal.find('#editResellerForm').attr('action', '/reseller/' + id);
    });
});

// Fungsi konfirmasi
function confirmDelete(event, resellerName) {
    event.preventDefault();
    const form = event.target.closest('form');
    
    Swal.fire({
        title: 'Hapus Reseller?',
        text: `Apakah Anda yakin ingin menghapus reseller "${resellerName}"?`,
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

function confirmToggleStatus(event, resellerName, isActive) {
    event.preventDefault();
    const form = event.target.closest('form');
    const action = isActive ? 'nonaktifkan' : 'aktifkan';
    
    Swal.fire({
        title: `${isActive ? 'Nonaktifkan' : 'Aktifkan'} Reseller?`,
        text: `Apakah Anda yakin ingin ${action} reseller "${resellerName}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `Ya, ${action}!`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>
@endpush