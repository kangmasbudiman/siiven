@extends('_layouts.app')

@section('title', 'Master Data - Aplikasi')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i> Data Aplikasi
                        </h4>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#createAplikasiModal">
                            <i class="fas fa-plus me-1"></i> Tambah Aplikasi
                        </button>


                    </div>
                    <div class="card-body">
                        <!-- TAMBAHKAN INI UNTUK DEBUG -->
                        <div id="debug-info" class="alert alert-info d-none">
                            <strong>Debug Info:</strong>
                            <div id="debug-content"></div>
                        </div>

                        <table id="aplikasi-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Aplikasi</th>
                                    <th>Code Aplikasi</th>
                                    <th>Price</th>
                                    <th>Coin Type</th>
                                    <th>Rate</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($aplikasis as $aplikasi)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $aplikasi->nameaplication }}</td>
                                        <td>{{ $aplikasi->code }}</td>
                                        <td>{{ $aplikasi->formatted_price }}</td> <!-- PANGGIL ACCESSOR -->
                                        <td>{{ $aplikasi->coin_type }}</td>
                                        <td>{{ $aplikasi->rate }}</td>
                                     
                                        <td>{{ $aplikasi->description }}</td>
                                        <td>
                                            @if ($aplikasi->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-danger">Non-Aktif</span>
                                            @endif
                                        </td>



                                        <td class="text">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editAplikasiModal" data-id="{{ $aplikasi->id }}"
                                                    data-nameaplication="{{ $aplikasi->nameaplication }}"
                                                    data-code="{{ $aplikasi->code }}"
                                                    data-coin_type="{{ $aplikasi->coin_type }}"
                                                    data-rate="{{ $aplikasi->rate }}"
                                                    data-normal_price="{{ $aplikasi->normal_price }}"
                                                    data-description="{{ $aplikasi->description }}"
                                                    data-is_active="{{ $aplikasi->is_active ? '1' : '0' }}" title="Edit">
                                                    <i class="fas fa-edit fa-fw"></i>
                                                </button>
                                                <span class="ms-2"> </span>
                                                <form action="{{ route('aplikasi.destroy', $aplikasi->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm ms-1"
                                                        onclick="return confirmDelete(event, '{{ $aplikasi->nameaplication }}')"
                                                        title="Hapus">
                                                        <i class="fas fa-trash fa-fw"></i>
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
    <div class="modal fade" id="editAplikasiModal" tabindex="-1" aria-labelledby="editAplikasiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editAplikasiForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editShiftModalLabel">Edit Aplikasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" id="edit_id" name="id" required>

                        <!-- Nama Aplikasi -->
                        <div class="mb-3">
                            <label for="edit_nameaplication" class="form-label">Nama Aplikasi</label>
                            <input type="text" class="form-control" id="edit_nameaplication" name="nameaplication"
                                required>
                        </div>

                        <!-- Code -->
                        <div class="mb-3">
                            <label for="edit_code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="edit_code" name="code" required>
                        </div>

                        <!-- Coin Type -->
                        <div class="mb-3">
                            <label for="edit_coin_type" class="form-label">Coin Type</label>
                            <input type="text" class="form-control" id="edit_coin_type" name="coin_type" required>
                        </div>

                        <!-- Rate -->
                        <div class="mb-3">
                            <label for="edit_rate" class="form-label">Rate</label>
                            <input type="text" class="form-control" id="edit_rate" name="rate" required>
                        </div>

                        <!-- Harga -->
                        <div class="mb-3">
                            <label for="edit_normal_price" class="form-label">Harga</label>
                            <input type="text" class="form-control" id="edit_normal_price" name="normal_price" required>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" id="edit_description" name="description" required>
                        </div>

                        <!-- Toggle Status Switch -->
                        <div class="mb-3">
                            <label for="edit_is_active" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <!-- ⭐⭐ PERBAIKAN: Hidden input untuk value 0 -->
                                <input type="hidden" name="is_active" value="0">
                                <!-- ⭐⭐ PERBAIKAN: Checkbox dengan value 1 -->
                                <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active"
                                    value="1">
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




    <!-- Modal Create aplikasi -->
    <div class="modal fade" id="createAplikasiModal" tabindex="-1" aria-labelledby="createAplikasiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('aplikasi.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createShiftModalLabel">Tambah Aplikasi Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Aplikasi</label>
                            <input type="text" class="form-control" id="nameaplication" name="nameaplication"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="mb-3">
                            <label for="normal_price" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="normal_price" name="normal_price" required>
                        </div>
                        <div class="mb-3">
                            <label for="coin_type" class="form-label">Coin Type</label>
                            <input type="text" class="form-control" id="coin_type" name="coin_type" required>
                        </div>
                        <div class="mb-3">
                            <label for="rate" class="form-label">Rate</label>
                            <input type="number" class="form-control" id="rate" name="rate" required>
                        </div>  
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>

                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label" for="is_active">Active</label>
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
            if ($('#aplikasi-table').length === 0) {
                console.error('❌ Table #aplikasi-table tidak ditemukan!');
                $('#debug-info').removeClass('d-none');
                $('#debug-content').html('Table dengan ID shifts-table tidak ditemukan!');
                return;
            }

            // CONFIGURASI PALING SIMPLE - HANYA FITUR DASAR
            try {
                var table = $('#aplikasi-table').DataTable({
                    // Fitur dasar
                    paging: true, // Pagination
                    searching: true, // Search box
                    ordering: true, // Sorting
                    info: true, // Show info
                    responsive: true, // Responsive
                    autoWidth: false, // Auto width

                    // Basic configuration
                    pageLength: 10, // Cuma 5 data per halaman untuk testing
                    lengthChange: true, // Show length change dropdown
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
                title: 'Hapus Aplikasi?',
                text: `Apakah Anda yakin ingin menghapus aplikasi "${shiftName}"?`,
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
        $('#editAplikasiModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); // tombol yang diklik

            const id = button.data('id');

            const nameaplication = button.data('nameaplication');
            const code = button.data('code');
            const normal_price = button.data('normal_price');
            const coin_type = button.data('coin_type');
            const rate = button.data('rate');
            const description = button.data('description');
            const is_active = button.data('is_active'); // true/false atau 1/0


            // Set data ke input form
            const modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_nameaplication').val(nameaplication);
            modal.find('#edit_code').val(code);
            modal.find('#edit_normal_price').val(normal_price);
            modal.find('#edit_coin_type').val(coin_type);
            modal.find('#edit_rate').val(rate);
            modal.find('#edit_description').val(description);

            // ⭐⭐ PERBAIKAN: Set checkbox status dengan prop()
            modal.find('#edit_is_active').prop('checked', is_active == 1 || is_active === true);

            console.log('Debug is_active:', {
                raw: is_active,
                type: typeof is_active,
                converted: (is_active == 1 || is_active === true)
            });

            // Ganti action form supaya mengarah ke route update
            const updateUrl = `/aplikasi/${id}`; // pastikan route update kamu: Route::put('/shift/{id}', ...)
            modal.find('#editAplikasiForm').attr('action', updateUrl);
        });
    </script>
@endpush
