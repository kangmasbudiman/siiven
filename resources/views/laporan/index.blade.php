@extends('_layouts.app')

@section('title', 'Laporan Barang - Laporan Data Barang Ruangan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa fa-file-alt me-2"></i>
                            Laporan Stok Barang
                        </h5>

                        <div class="btn-group">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse"
                                data-bs-target="#filterLaporan">
                                <i class="fa fa-filter me-1"></i> Filter
                            </button>

                            <a href="{{ route('laporan.pdf') }}" class="btn btn-outline-danger btn-sm">
                                <i class="fa fa-file-pdf me-1"></i> PDF
                            </a>

                            <a href="{{ route('laporan.excel') }}" class="btn btn-outline-success btn-sm">
                                <i class="fa fa-file-excel me-1"></i> Excel
                            </a>
                        </div>
                    </div>
                    <div id="filterLaporan" class="collapse mb-3">
                        <div class="card card-body">

                            <form method="GET" action="{{ route('laporan.pdf') }}">
                                <div class="row g-2">

                                    <div class="col-md-3">
                                        <select name="ruangan_id" class="form-control">
                                            <option value="">Semua Ruangan</option>
                                            @foreach ($ruangans as $ruangan)
                                                <option value="{{ $ruangan->id }}"
                                                    {{ request('ruangan_id') == $ruangan->id ? 'selected' : '' }}>
                                                    {{ $ruangan->nama_ruangan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <select name="barang_id" class="form-control">
                                            <option value="">Semua Barang</option>
                                            @foreach ($barangs as $barang)
                                                <option value="{{ $barang->id }}"
                                                    {{ request('barang_id') == $barang->id ? 'selected' : '' }}>
                                                    {{ $barang->nama_barang }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <select name="kondisi_id" class="form-control">
                                            <option value="">Semua Kondisi</option>
                                            @foreach ($kondisis as $kondisi)
                                                <option value="{{ $kondisi->id }}"
                                                    {{ request('kondisi_id') == $kondisi->id ? 'selected' : '' }}>
                                                    {{ $kondisi->nama_kondisi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3 text-end">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-search me-1"></i> Tampilkan
                                        </button>
                                    </div>

                                </div>
                            </form>

                        </div>
                    </div>
                    <div class="card-body">


                        <table id="ruangan-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Ruangan</th>
                                    <th>Nama Barang</th>
                                    <th>Nomor Inventaris</th>
                                     <th>Tanggal Pembelian</th>
                                    <th>Tanggal Penerimaan</th>
                                   
                                    <th>Nomor Seri</th>
                                    <th>Kondisi Barang</th>
                                    <th>Kategori Barang</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Satuan</th>
                                    <th>Keterangan</th>

                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventaris as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->ruangan->nama_ruangan }}</td>
                                    
                                        <td>{{ $item->barang->nama_barang }}</td>
                                            <td>{{ $item->nomorInventaris }}</td>
                                        <td>{{ $item->tanggalPembelian }}</td>
                                        <td>{{ $item->tanggalPenerimaan }}</td>
                                        <td>{{ $item->nomorSeri }}</td>
                                        <td>{{ $item->kondisi->nama_kondisi }}</td>
                                        <td>{{ $item->barang->kategori->nama_kategori }}</td>
                                        <td>{{ $item->jumlah }}</td>
                                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td>{{ $item->barang->satuan }}</td>
                                        <td>{{ $item->keterangan }}</td>

                                        </td>


                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editKondisiModal" data-id="{{ $item->id }}"
                                                    data-nama_barang="{{ $item->nama_barang }}"
                                                    data-keterangan="{{ $item->keterangan }}"
                                                    data-is_active="{{ $item->is_active }}" title="Lihat">
                                                    <i class="fas fa-edit"></i>
                                                </button>

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
                title: 'Hapus Kondisi?',
                text: `Apakah Anda yakin ingin menghapus kondisi "${shiftName}"?`,
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
        $('#editKondisiModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); // tombol yang diklik

            const id = button.data('id');
            const keterangan = button.data('keterangan');
            const nama_kondisi = button.data('nama_kondisi');
            const is_active = button.data('is_active');


            // Set data ke input form
            const modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_keterangan').val(keterangan);
            modal.find('#edit_nama_kondisi').val(nama_kondisi);


            // RESET dulu (penting)
            modal.find('#edit_is_active').prop('checked', false);

            // Jika is_active = 1 → checkbox dicentang
            if (parseInt(is_active) === 1) {
                modal.find('#edit_is_active').prop('checked', true);
            }

            // Ganti action form supaya mengarah ke route update
            const updateUrl = `/kondisi/${id}`; // pastikan route update kamu: Route::put('/shift/{id}', ...)
            modal.find('#editKondisiForm').attr('action', updateUrl);

        });
    </script>
@endpush
