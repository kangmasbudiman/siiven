@extends('_layouts.app')

@section('title', 'Master Data - Barang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-box me-2"></i> Data Barang
                        </h4>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#createBarangModal">
                            <i class="fas fa-plus me-1"></i> Tambah Barang
                        </button>


                    </div>
                    <div class="card-body">


                        <table id="barang-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Jenis Barang</th>
                                    <th>Merk</th>
                                    <th>Satuan</th>
                                    <th>Spesifikasi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barangs as $barang)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $barang->kode_barang }}</td>
                                        <td>{{ $barang->nama_barang }}</td>
                                        <td>{{ $barang->kategori->nama_kategori }}</td>
                                        <td>{{ $barang->jenis_barang }}</td>
                                        <td>{{ $barang->merk }}</td>
                                        <td>{{ $barang->satuan }}</td>
                                        <td>{{ $barang->spesifikasi }}</td>
                                          <td class="text-center">
                                            @if ($barang->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>




                                     
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editBarangModal" data-id="{{ $barang->id }}"
                                                    data-kode_barang="{{ $barang->kode_barang }}"
                                                    data-nama_barang="{{ $barang->nama_barang }}"
                                                    data-kategori_id="{{ $barang->kategori_id }}"
                                                    data-jenis_barang="{{ $barang->jenis_barang }}"
                                                    data-merk="{{ $barang->merk }}"
                                                    data-satuan="{{ $barang->satuan }}"
                                                    data-spesifikasi="{{ $barang->spesifikasi }}"
                                                    data-is_active="{{ $barang->is_active }}" title="Lihat">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('barang.destroy', $barang->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirmDelete(event, '{{ $barang->nama_barang }}')"
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


    <!-- Modal Edit Barang -->
    <div class="modal fade" id="editBarangModal" tabindex="-1" aria-labelledby="editBarangModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editBarangForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="editBarangModalLabel">Edit Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Hidden ID -->
                        <input type="hidden" id="edit_id" name="id">

                        <!-- Nama Kategori -->

                    

                        <div class="mb-3">
                            <label for="edit_nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="edit_nama_barang" name="nama_barang"
                                required>
                        </div>

                      
                        <!-- Kategori -->
                        <div class="mb-3">
                            <label for="edit_kategori_id" class="form-label">Kategori</label>
                            <select class="form-control" id="edit_kategori_id" name="kategori_id" required>
                                @foreach ($kategori_barangs as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Jenis Barang -->
                        <div class="mb-3">
                            <label for="edit_jenis_barang" class="form-label">Jenis Barang</label>
                            <select class="form-control" id="edit_jenis_barang" name="jenis_barang" required>
                                <option value="">-- Pilih Jenis Barang --</option>
                                <option value="BHP">BHP</option>
                                <option value="Aset">Aset</option>
                            </select>
                        </div>
                        <!-- Merk -->
                        <div class="mb-3">
                            <label for="edit_merk" class="form-label">Merk</label>
                            <input type="text" class="form-control" id="edit_merk" name="merk" required>
                        </div>
                        <!-- Satuan -->
                        <div class="mb-3">
                            <label for="edit_satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="edit_satuan" name="satuan" required>
                        </div>
                        <!-- Spesifikasi -->
                        <div class="mb-3">
                            <label for="edit_spesifikasi" class="form-label">Spesifikasi</label>
                            <input type="text" class="form-control" id="edit_spesifikasi" name="spesifikasi" required>
                        </div>
                        <!-- Status -->
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

                        <!-- Status -->
                   
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




    <!-- Modal Create Barang -->
    <div class="modal fade" id="createBarangModal" tabindex="-1" aria-labelledby="createBarangModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('barang.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createBarangModalLabel">Tambah Barang Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Kode Barang -->
                     
                        <div class="mb-3">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                        </div>
                        <!-- Kategori -->
                        <div class="mb-3">
                            <label for="kategori_id" class="form-label">Kategori</label>
                            <select class="form-control" id="kategori_id" name="kategori_id" required>
                                @foreach ($kategori_barangs as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Jenis Barang -->
                        <div class="mb-3">
                            <label for="jenis_barang" class="form-label">Jenis Barang</label>
                            <select class="form-control" id="jenis_barang" name="jenis_barang" required>
                                <option value="">-- Pilih Jenis Barang --</option>
                                <option value="Aset">Aset</option>
                                <option value="BHP">BHP</option>
                            </select>
                        </div>
                        <!-- Merk -->
                        <div class="mb-3">
                            <label for="merk" class="form-label">Merk</label>
                            <input type="text" class="form-control" id="merk" name="merk" required>
                        </div>
                        <!-- Satuan -->
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="satuan" name="satuan" required>
                        </div>
                        <!-- Spesifikasi -->
                        <div class="mb-3">
                            <label for="spesifikasi" class="form-label">Spesifikasi</label>
                            <input type="text" class="form-control" id="spesifikasi" name="spesifikasi" required>
                        </div>
                        <!-- Status -->
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
            if ($('#barang-table').length === 0) {
                console.error('❌ Table #barang-table tidak ditemukan!');
                $('#debug-info').removeClass('d-none');
                $('#debug-content').html('Table dengan ID barang-table tidak ditemukan!');
                return;
            }

            // CONFIGURASI PALING SIMPLE - HANYA FITUR DASAR
            try {
                var table = $('#barang-table').DataTable({
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
                title: 'Hapus Barang?',
                text: `Apakah Anda yakin ingin menghapus barang "${shiftName}"?`,
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
        $('#editBarangModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); // tombol yang diklik

            const id = button.data('id');
            const kode_barang = button.data('kode_barang');
            const nama_barang = button.data('nama_barang');
            const kategori_id = button.data('kategori_id');
            const jenis_barang = button.data('jenis_barang');
            const merk = button.data('merk');
            const satuan = button.data('satuan');
            const spesifikasi = button.data('spesifikasi');
            const is_active = button.data('is_active');


            // Set data ke input form
            const modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_kode_barang').val(kode_barang);
            modal.find('#edit_nama_barang').val(nama_barang);
            modal.find('#edit_kategori_id').val(kategori_id);
            modal.find('#edit_jenis_barang').val(jenis_barang);
            modal.find('#edit_merk').val(merk);
            modal.find('#edit_satuan').val(satuan);
            modal.find('#edit_spesifikasi').val(spesifikasi);


            // RESET dulu (penting)
            modal.find('#edit_is_active').prop('checked', false);

            // Jika is_active = 1 → checkbox dicentang
            if (parseInt(is_active) === 1) {
                modal.find('#edit_is_active').prop('checked', true);
            }

            // Ganti action form supaya mengarah ke route update
            const updateUrl = `/barang/${id}`; // pastikan route update kamu: Route::put('/shift/{id}', ...)
            modal.find('#editBarangForm').attr('action', updateUrl);

        });
    </script>
@endpush
