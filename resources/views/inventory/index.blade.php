@extends('_layouts.app')

@section('title', 'Master Data - Inventory Barang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-university me-2"></i> Data Inventory Barang
                        </h4>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#createInventoryModal">
                            <i class="fas fa-plus me-1"></i> Tambah Inventory Barang
                        </button>


                    </div>
                    <div class="card-body">


                        <table id="ruangan-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>

                                    <th>Nama Barang</th>
                                    <th>NOMOR INVENTARIS</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Kondisi</th>
                                    <th>User Entri</th>
                                    <th>Tanggal Pembelian</th>
                                     <th>Tanggal Penerimaan</th>
                                    <th>Merk</th>
                                    <th>Type</th>
                                    <th>Nomor Seri</th>
                                    <th>Kondisi Pembelian</th>
                                   
                                    <th>Keterangan</th>

                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventories as $inventory)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $inventory->barang->nama_barang }}</td>
                                        <td>{{ $inventory->nomorInventaris }}</td>
                                        <td>{{ $inventory->jumlah }}</td>
                                        <td>Rp {{ number_format($inventory->harga, 0, ',', '.') }}</td>
                                        <td>{{ $inventory->kondisi->nama_kondisi }}</td>
                                        <td>{{ $inventory->user->nama }}</td>
                                        <td>{{ $inventory->tanggalPembelian }}</td>
                                         <td>{{ $inventory->tanggalPenerimaan }}</td>
                                        <td>{{ $inventory->merk }}</td>
                                        <td>{{ $inventory->type }}</td>
                                        <td>{{ $inventory->nomorSeri }}</td>
                                        <td>{{ $inventory->kondisiPembelian }}</td>
                                       
                                        <td>{{ $inventory->keterangan }}</td>





                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editInventoryModal" data-id="{{ $inventory->id }}"
                                                    data-barang_id="{{ $inventory->barang_id }}"
                                                    data-ruangan_id="{{ $inventory->ruangan_id }}"
                                                    data-jumlah="{{ $inventory->jumlah }}"
                                                    data-harga="{{ $inventory->harga }}"
                                                    data-kondisi_id="{{ $inventory->kondisi_id }}"
                                                    data-user_id="{{ $inventory->user_id }}"
                                                    data-tangga-pembelian="{{ $inventory->tanggalPembelian }}"
                                                    data-tanggal-penerimaan="{{ $inventory->tanggalPenerimaan }}"
                                                    data-merk="{{ $inventory->merk }}"
                                                    data-type="{{ $inventory->type }}"
                                                    data-nomor-seri="{{ $inventory->nomorSeri }}"
                                                    data-kondisi-pembelian="{{ $inventory->kondisiPembelian }}"
                                                  
                                                    data-keterangan="{{ $inventory->keterangan }}" >
                                                <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('inventory.destroy', $inventory->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirmDelete(event, '{{ $inventory->barang->nama_barang }}')"
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


    <!-- Modal Edit Inventory -->
    <div class="modal fade" id="editInventoryModal" tabindex="-1" aria-labelledby="editInventoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editInventoryForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="editInventoryModalLabel">Edit Inventory</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Hidden ID -->
                        <input type="hidden" id="edit_id" name="id">

                        <!-- Nama Barang -->
                        <div class="mb-3">
                            <label for="edit_nama_barang" class="form-label">Nama Barang</label>


                            <select id="edit_barang_id" name="barang_id" required>
                                <option value="">Pilih Barang</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->id }}" data-code="{{ $barang->kode_barang }}">
                                        {{ $barang->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jumlah -->
                        <div class="mb-3">
                            <label for="edit_jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="edit_jumlah" name="jumlah" required>
                        </div>


                        <!-- Kondisi -->
                        <div class="mb-3">
                            <label for="edit_kondisi" class="form-label">Kondisi</label>



                            <select class="form-control" id="edit_kondisi_id" name="kondisi_id" required>
                                <option value="">-- Pilih Kondisi --</option>
                                @foreach ($kondisis as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_kondisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Harga -->

                        <div class="mb-3">
                            <label for="edit_harga" class="form-label">Estimasi Harga</label>
                            <input type="number" class="form-control" id="edit_harga" name="harga" required>
                        </div>

                         <div class="mb-3">
                            <label for="edit_tanggal-pembelian" class="form-label">Tanggal Pembelian</label>
                            <input type="date" class="form-control" id="edit_tanggal-pembelian" name="tanggal-pembelian" required>
                        </div>
                         <!-- Merk -->
                        <div class="mb-3">
                            <label for="edit_merk" class="form-label">Merk</label>
                            <input type="text" class="form-control" id="edit_merk" name="merk" required>
                        </div>
                         <!-- Type -->
                        <div class="mb-3">
                            <label for="edit_type" class="form-label">Type</label>
                            <input type="text" class="form-control" id="edit_type" name="type" required>
                        </div>
                         <!-- Nomor Seri -->
                        <div class="mb-3">
                            <label for="edit_nomor-seri" class="form-label">Nomor Seri</label>
                            <input type="text" class="form-control" id="edit_nomor-seri" name="nomor-seri" required>
                        </div>
                         <!-- Kondisi Pembelian -->
                        <div class="mb-3">
                           
                            <label for="edit_kondisi-pembelian" class="form-label">Kondisi Pembelian</label>
                            <select class="form-control" id="edit_kondisi-pembelian" name="kondisi-pembelian" required>
                                <option value="">-- Pilih Kondisi Pembelian --</option>
                                <option value="Brand New">Brand New</option>
                                <option value="Second">Second</option>


                            </select>
                     
                        </div>
                         <!-- Tanggal Penerimaan -->
                        <div class="mb-3">
                            <label for="edit_tanggal-penerimaan" class="form-label">Tanggal Penerimaan</label>
                            <input type="date" class="form-control" id="edit_tanggal-penerimaan" name="tanggal-penerimaan" required>
                        </div>




                        <div class="mb-3">
                            <label for="edit_keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="edit_keterangan" name="keterangan" required>
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




    <!-- Modal Create Inventory -->
    <div class="modal fade" id="createInventoryModal" tabindex="-1" aria-labelledby="createInventoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createInventoryModalLabel">Tambah Inventory Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_barang_id" class="form-label">Barang</label>


                            <select id="barang_id" name="barang_id" required>
                                <option value="">Pilih Barang</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->id }}" data-code="{{ $barang->kode_barang }}">
                                        {{ $barang->nama_barang }}
                                    </option>
                                @endforeach
                            </select>




                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                        </div>
                        <div class="mb-3">
                            <label for="kondisi" class="form-label">Kondisi</label>
                            <select class="form-control" id="kondisi" name="kondisi_id" required>
                                <option value="">-- Pilih Kondisi --</option>
                                @foreach ($kondisis as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_kondisi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Harga -->
                        <div class="mb-3">
                            <label for="harga" class="form-label">Estimasi Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" required>
                        </div>

                        <div class="mb-3">
                            <label for="tanggalPembelian" class="form-label">Tanggal Pembelian</label>
                            <input type="date" class="form-control" id="tanggalPembelian" name="tanggalPembelian"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="merk" class="form-label">Merk</label>
                            <input type="text" class="form-control" id="merk" name="merk" >
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <input type="text" class="form-control" id="type" name="type" >
                        </div>
                        <div class="mb-3">
                            <label for="nomorSeri" class="form-label">Nomor Seri</label>
                            <input type="text" class="form-control" id="nomorSeri" name="nomorSeri" >
                        </div>
                        <div class="mb-3">
                            <label for="kondisiPembelian" class="form-label">Kondisi Pembelian</label>
                            <select class="form-control" id="kondisiPembelian" name="kondisiPembelian" required>
                                <option value="">-- Pilih Kondisi Pembelian --</option>
                                <option value="Brand New">Brand New</option>
                                <option value="Second">Second</option>


                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tanggalPenerimaan" class="form-label">Tanggal Penerimaan</label>
                            <input type="date" class="form-control" id="tanggalPenerimaan" name="tanggalPenerimaan"
                                required>
                        </div>







                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" required>
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
            if ($('#ruangan-table').length === 0) {
                console.error('❌ Table #ruangan-table tidak ditemukan!');
                $('#debug-info').removeClass('d-none');
                $('#debug-content').html('Table dengan ID ruangan-table tidak ditemukan!');
                return;
            }

            // CONFIGURASI PALING SIMPLE - HANYA FITUR DASAR
            try {
                var table = $('#ruangan-table').DataTable({
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
        $('#editInventoryModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); // tombol yang diklik

            const id = button.data('id');
            const keterangan = button.data('keterangan');
            const barang_id = button.data('barang_id');
            const jumlah = button.data('jumlah');
            const kondisi_id = button.data('kondisi_id');
            const harga = button.data('harga');
            const tanggalPembelian = button.data('tanggalPembelian');
            const merk = button.data('merk');
            const type = button.data('type');
            const nomorSeri = button.data('nomor-seri');
            const kondisiPembelian = button.data('kondisi-pembelian');
            const tanggalPenerimaan = button.data('tanggal-penerimaan');



            // Set data ke input form
            const modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_keterangan').val(keterangan);
            modal.find('#edit_barang_id').val(barang_id);
            modal.find('#edit_jumlah').val(jumlah);
            modal.find('#edit_harga').val(harga);
            modal.find('#edit_kondisi_id').val(kondisi_id);
            modal.find('#edit_tanggal-pembelian').val(tanggalPembelian);
            modal.find('#edit_merk').val(merk);
            modal.find('#edit_type').val(type);
            modal.find('#edit_nomor-seri').val(nomorSeri);
            modal.find('#edit_kondisi-pembelian').val(kondisiPembelian);
            modal.find('#edit_tanggal-penerimaan').val(tanggalPenerimaan);
            // 🔥 INI KUNCI UNTUK TOM SELECT
            editBarangSelect.setValue(barang_id);
            // Ganti action form supaya mengarah ke route update
            const updateUrl = `/inventory/${id}`; // pastikan route update kamu: Route::put('/shift/{id}', ...)
            modal.find('#editInventoryForm').attr('action', updateUrl);

        });
    </script>
@endpush
@push('js')
    <script>
        let createBarangSelect;
        let editBarangSelect;

        document.addEventListener('DOMContentLoaded', function() {

            // CREATE MODAL
            createBarangSelect = new TomSelect('#barang_id', {
                placeholder: 'Cari barang...',
                allowEmptyOption: true,
                render: {
                    option: function(data, escape) {
                        return `
                    <div>
                        <strong>${escape(data.text)}</strong><br>
                        <small class="text-muted">${escape(data.code || '')}</small>
                    </div>
                `;
                    }
                }
            });

            // EDIT MODAL
            editBarangSelect = new TomSelect('#edit_barang_id', {
                placeholder: 'Cari barang...',
                allowEmptyOption: true,
                render: {
                    option: function(data, escape) {
                        return `
                    <div>
                        <strong>${escape(data.text)}</strong><br>
                        <small class="text-muted">${escape(data.code || '')}</small>
                    </div>
                `;
                    }
                }
            });

        });
    </script>
@endpush
