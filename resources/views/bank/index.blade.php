@extends('_layouts.app')

@section('title', 'Master Data - Bank')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-university me-2"></i>Data Bank & Rekening
                    </h4>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createBankModal">
                        <i class="fas fa-plus me-1"></i>Tambah Rekening
                    </button>
                </div>
                <div class="card-body">
                    <!-- Bank Group Summary -->
                    <div class="row mb-4">
                        @php
                            $bankGroups = $banks->groupBy('bank_name');
                        @endphp
                        @foreach($bankGroups as $bankName => $bankAccounts)
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white py-2">
                                    <h6 class="mb-0">
                                        <i class="fas fa-landmark me-1"></i> {{ $bankName }}
                                        <span class="badge bg-light text-dark ms-2"> {{ $bankAccounts->count() }} rekening</span>
                                    </h6>
                                </div>
                                <div class="card-body p-2">
                                    @foreach($bankAccounts as $account)
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                        <small>
                                            <strong>{{ $account->getFormattedAccountNumberAttribute() }}</strong>
                                            <br>
                                            <span class="text-muted">{{ $account->account_name }}</span>
                                        </small>
                                        @if($account->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Non-Aktif</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Detailed Table -->
                    <table id="banks-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Bank</th>
                                <th>No Rekening</th>
                                <th>Nama Pemilik</th>
                                <th>Cabang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banks as $bank)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $bank->bank_name }}</strong>
                                </td>
                                <td>
                                    <code>{{ $bank->getFormattedAccountNumberAttribute() }}</code>
                                </td>
                                <td>{{ $bank->account_name }}</td>
                                <td>{{ $bank->branch ?? '-' }}</td>
                                <td class="text-center">
                                    @if($bank->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editBankModal" 
                                            data-id="{{ $bank->id }}"
                                            data-bank_name="{{ $bank->bank_name }}"
                                            data-account_number="{{ $bank->account_number }}"
                                            data-account_name="{{ $bank->account_name }}"
                                            data-branch="{{ $bank->branch }}"
                                            data-notes="{{ $bank->notes }}"
                                            data-is_active="{{ $bank->is_active ? '1' : '0' }}" 
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <form action="{{ route('bank.toggle-status', $bank->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('patch')
                                            <button type="submit" class="btn {{ $bank->is_active ? 'btn-secondary' : 'btn-success' }}"
                                                    onclick="return confirmToggleStatus(event, '{{ $bank->bank_name }}', {{ $bank->is_active }})"
                                                    title="{{ $bank->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas {{ $bank->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                            </button>
                                        </form>

                                        @if($bank->canDelete())
                                        <form action="{{ route('bank.destroy', $bank->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirmDelete(event, '{{ $bank->bank_name }} - {{ $bank->getFormattedAccountNumberAttribute() }}')"
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
@include('bank.modals')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi DataTables
    $('#banks-table').DataTable({
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
            { searchable: false, targets: [0, 4, 5, 6] }
        ],
        order: [[1, 'asc']],
        pageLength: 10
    });

    // Handle edit modal
    $('#editBankModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var bank_name = button.data('bank_name');
        var account_number = button.data('account_number');
        var account_name = button.data('account_name');
        var branch = button.data('branch');
        var notes = button.data('notes');
        var is_active = button.data('is_active');
console.log(id);
        var modal = $(this);
        modal.find('#edit_id').val(id);
        modal.find('#edit_bank_name').val(bank_name);
        modal.find('#edit_account_number').val(account_number);
        modal.find('#edit_account_name').val(account_name);
        modal.find('#edit_branch').val(branch);
        modal.find('#edit_notes').val(notes);
        modal.find('#edit_is_active').prop('checked', is_active == '1');

          // Ganti action form supaya mengarah ke route update
            const updateUrl = `/bank/${id}`; // pastikan route update kamu: Route::put('/shift/{id}', ...)
            modal.find('#editBankForm').attr('action', updateUrl);
    });
});

// Fungsi konfirmasi
function confirmDelete(event, bankName) {
    event.preventDefault();
    const form = event.target.closest('form');
    
    Swal.fire({
        title: 'Hapus Bank?',
        text: `Apakah Anda yakin ingin menghapus bank "${bankName}"?`,
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

function confirmToggleStatus(event, bankName, isActive) {
    event.preventDefault();
    const form = event.target.closest('form');
    const action = isActive ? 'nonaktifkan' : 'aktifkan';
    
    Swal.fire({
        title: `${isActive ? 'Nonaktifkan' : 'Aktifkan'} Bank?`,
        text: `Apakah Anda yakin ingin ${action} bank "${bankName}"?`,
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