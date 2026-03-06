@extends('_layouts.app')

@section('title', 'Master Data - Kategori Barang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-university me-2"></i> Data Kategori Barang
                        </h4>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#createKategoriModal">
                            <i class="fas fa-plus me-1"></i> Tambah Kategori Barang
                        </button>


                    </div>
                    <div class="card-body">


                        <table id="ruangan-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                  
                                    <th>Nama Kategori</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kategoris as $kategori)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $kategori->nama_kategori }}</td>
                                        <td>{{ $kategori->keterangan }}</td>
                                     
                                        </td>



                                        <td class="text-center">
                                            @if ($kategori->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editKategoriModal" data-id="{{ $kategori->id }}"
                                                    data-nama_kategori="{{ $kategori->nama_kategori }}"
                                                    data-keterangan="{{ $kategori->keterangan }}"
                                                  
                                                    data-is_active="{{ $kategori->is_active }}" title="Lihat">
<i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirmDelete(event, '{{ $kategori->nama_kategori }}')"
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


    <!-- Modal Edit Kategori -->
  <div class="modal fade" id="editKategoriModal" tabindex="-1" aria-labelledby="editKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editKategoriForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editKategoriModalLabel">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Hidden ID -->
                    <input type="hidden" id="edit_id" name="id">

                    <!-- Nama Kategori -->
                    <div class="mb-3">
                        <label for="edit_nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" required>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="edit_keterangan" name="keterangan">
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status Aktif</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox"
                                   id="edit_is_active" name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">Aktif</label>
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

</div>




    </div>




    <!-- Modal Create Kategori -->
    <div class="modal fade" id="createKategoriModal" tabindex="-1" aria-labelledby="createKategoriModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('kategori.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createKategoriModalLabel">Tambah Kategori Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori</label>
                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                        </div>
                
               
        







                        <div class="mb-3">
                            <label for="is_active" class="form-label">Status Aktif</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label" for="is_active">Aktif</label>
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
                title: 'Hapus Kategori?',
                text: `Apakah Anda yakin ingin menghapus kategori "${shiftName}"?`,
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

                }
            });
        }




        // Handle modal edit
        $('#editKategoriModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); // tombol yang diklik

            const id = button.data('id');
            const keterangan = button.data('keterangan');
            const nama_kategori = button.data('nama_kategori');
          
            const is_active = button.data('is_active');

            






            // Set data ke input form
            const modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_keterangan').val(keterangan);
            modal.find('#edit_nama_kategori').val(nama_kategori);
           
            
             // RESET dulu (penting)
    modal.find('#edit_is_active').prop('checked', false);

    // Jika is_active = 1 → checkbox dicentang
    if (parseInt(is_active) === 1) {
        modal.find('#edit_is_active').prop('checked', true);
    }

            // Ganti action form supaya mengarah ke route update
            const updateUrl = `/kategori/${id}`; // pastikan route update kamu: Route::put('/shift/{id}', ...)
            modal.find('#editKategoriForm').attr('action', updateUrl);

        });
    </script>
@endpush
