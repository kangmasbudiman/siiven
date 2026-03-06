@extends('_layouts.app')

@section('title', 'Master Data - Ruangan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-university me-2"></i> Data Ruangan
                        </h4>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#createRuanganModal">
                            <i class="fas fa-plus me-1"></i> Tambah Ruangan
                        </button>


                    </div>
                    <div class="card-body">


                        <table id="ruangan-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Ruangan</th>
                                    <th>Nama Ruangan</th>
                                    <th>Jenis Ruangan</th>
                                    <th>Gedung</th>
                                    <th>Lantai</th>
                                    <th>Penanggung Jawab</th>
                                    <th>Jabatan</th>
                                    <th>Kontak</th>
                                    <th>User Entri</th>
                                    <th>Approver (Kabag/Kabid)</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ruangans as $ruangan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $ruangan->kode_ruangan }}</td>
                                        <td>{{ $ruangan->nama_ruangan }}</td>
                                        <td>{{ $ruangan->jenis_ruangan }}</td>
                                        <td>{{ $ruangan->gedung }}</td>
                                        <td>{{ $ruangan->lantai }}</td>
                                        <td>{{ $ruangan->penanggung_jawab }}</td>
                                        <td>{{ $ruangan->jabatan }}</td>
                                        <td>{{ $ruangan->kontak }}</td>
                                        <td>{{ $ruangan->user ? $ruangan->user->nama : '-' }}</td>
                                        <td>
                                            @if($ruangan->approver)
                                                <span class="badge bg-info text-dark">{{ $ruangan->approver->nama }}</span>
                                                @if($ruangan->approver->jabatan)
                                                    <br><small class="text-muted">{{ $ruangan->approver->jabatan }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($ruangan->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editRuanganModal" data-id="{{ $ruangan->id }}"
                                                    data-kode_ruangan="{{ $ruangan->kode_ruangan }}"
                                                    data-nama_ruangan="{{ $ruangan->nama_ruangan }}"
                                                    data-jenis_ruangan="{{ $ruangan->jenis_ruangan }}"
                                                    data-gedung="{{ $ruangan->gedung }}"
                                                    data-lantai="{{ $ruangan->lantai }}"
                                                    data-penanggung_jawab="{{ $ruangan->penanggung_jawab }}"
                                                    data-jabatan="{{ $ruangan->jabatan }}"
                                                    data-kontak="{{ $ruangan->kontak }}"
                                                    data-user="{{ $ruangan->user ? $ruangan->user->name : 'Tidak ada' }}"
                                                    data-approver_id="{{ $ruangan->approver_id }}"
                                                    data-is_active="{{ $ruangan->is_active }}" title="Edit">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <form action="{{ route('ruangan.destroy', $ruangan->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="return confirmDelete(event, '{{ $ruangan->kode_ruangan }}')"
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


    <!-- Modal Edit Ruangan -->
 <div class="modal fade" id="editRuanganModal" tabindex="-1" aria-labelledby="editRuanganModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editRuanganForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="editRuanganModalLabel">Edit Ruangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Hidden ID -->
                    <input type="hidden" id="edit_id" name="id">

                    <!-- Kode Ruangan -->
                    <div class="mb-3">
                        <label for="edit_kode_ruangan" class="form-label">Kode Ruangan</label>
                        <input type="text" class="form-control" id="edit_kode_ruangan" name="kode_ruangan" required>
                    </div>

                    <!-- Nama + Jenis -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_nama_ruangan" class="form-label">Nama Ruangan</label>
                            <input type="text" class="form-control" id="edit_nama_ruangan" name="nama_ruangan" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_jenis_ruangan" class="form-label">Jenis Ruangan</label>
                            <select class="form-control" id="edit_jenis_ruangan" name="jenis_ruangan" required>
                                <option value="">Pilih Jenis Ruangan</option>
                                <option value="pelayanan_medis">Pelayanan Medis</option>
                                <option value="penunjang">Penunjang</option>
                                <option value="logistik">Logistik</option>
                                <option value="administratif">Administratif</option>
                            </select>
                        </div>
                    </div>

                    <!-- Gedung + Lantai -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_gedung" class="form-label">Gedung</label>
                            <input type="text" class="form-control" id="edit_gedung" name="gedung" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_lantai" class="form-label">Lantai</label>
                            <input type="text" class="form-control" id="edit_lantai" name="lantai" required>
                        </div>
                    </div>

                    <!-- Penanggung Jawab + Jabatan -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_penanggung_jawab" class="form-label">Penanggung Jawab</label>
                            <input type="text" class="form-control" id="edit_penanggung_jawab"
                                name="penanggung_jawab" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="edit_jabatan" name="jabatan" required>
                        </div>
                    </div>

                    <!-- Kontak -->
                    <div class="mb-3">
                        <label for="edit_kontak" class="form-label">Kontak</label>
                        <input type="text" class="form-control" id="edit_kontak" name="kontak" required>
                    </div>

                    <!-- Approver level-1 -->
                    <div class="mb-3">
                        <label for="edit_approver_id" class="form-label">Approver (Kabag / Kabid)</label>
                        <select class="form-control" id="edit_approver_id" name="approver_id">
                            <option value="">-- Pilih Approver --</option>
                            @foreach($approvers as $ap)
                                <option value="{{ $ap->idUser }}">{{ $ap->nama }}{{ $ap->jabatan ? ' - ' . $ap->jabatan : '' }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Approver yang berhak menyetujui usulan dari ruangan ini</small>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status Aktif</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
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




    <!-- Modal Create Ruangan -->
    <div class="modal fade" id="createRuanganModal" tabindex="-1" aria-labelledby="createRuanganModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('ruangan.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createRuanganModalLabel">Tambah Ruangan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="kode_ruangan" class="form-label">Kode Ruangan</label>
                            <input type="text" class="form-control" id="kode_ruangan" name="kode_ruangan" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
                            <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">

                                <label for="edit_jenis_ruangan" class="form-label">Jenis Ruangan</label>
                                <select class="form-control" id="edit_jenis_ruangan" name="jenis_ruangan" required>
                                    <option value="">Pilih Jenis Ruangan</option>
                                    <option value="pelayanan_medis">Pelayanan Medis</option>
                                    <option value="penunjang">Penunjang</option>
                                    <option value="logistik">Logistik</option>
                                    <option value="administratif">Administratif</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gedung" class="form-label">Gedung</label>
                                <input type="text" class="form-control" id="gedung" name="gedung" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="lantai" class="form-label">Lantai</label>
                                <input type="text" class="form-control" id="lantai" name="lantai" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="penanggung_jawab" class="form-label">Penanggung Jawab</label>
                                <input type="text" class="form-control" id="penanggung_jawab" name="penanggung_jawab"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kontak" class="form-label">Kontak</label>
                                <input type="number" class="form-control" id="kontak" name="kontak" required>
                            </div>
                        </div>








                        <div class="mb-3">
                            <label for="approver_id" class="form-label">Approver (Kabag / Kabid)</label>
                            <select class="form-control" id="approver_id" name="approver_id">
                                <option value="">-- Pilih Approver --</option>
                                @foreach($approvers as $ap)
                                    <option value="{{ $ap->idUser }}">{{ $ap->nama }}{{ $ap->jabatan ? ' - ' . $ap->jabatan : '' }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Approver yang berhak menyetujui usulan dari ruangan ini</small>
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

                }
            });
        }




        // Handle modal edit
        $('#editRuanganModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget); // tombol yang diklik

            const id = button.data('id');
            const kode_ruangan = button.data('kode_ruangan');
            const nama_ruangan = button.data('nama_ruangan');
            const jenis_ruangan = button.data('jenis_ruangan');
            const gedung = button.data('gedung');
            const lantai = button.data('lantai');
            const penanggung_jawab = button.data('penanggung_jawab');
            const jabatan = button.data('jabatan');
            const kontak = button.data('kontak');
            const user_id = button.data('user_id');
            const is_active = button.data('is_active');
            const approver_id = button.data('approver_id');







            // Set data ke input form
            const modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_kode_ruangan').val(kode_ruangan);
            modal.find('#edit_nama_ruangan').val(nama_ruangan);
            modal.find('#edit_jenis_ruangan').val(jenis_ruangan);
            modal.find('#edit_gedung').val(gedung);
            modal.find('#edit_lantai').val(lantai);
            modal.find('#edit_penanggung_jawab').val(penanggung_jawab);
            modal.find('#edit_jabatan').val(jabatan);
            modal.find('#edit_kontak').val(kontak);
            modal.find('#edit_user_id').val(user_id);
            modal.find('#edit_approver_id').val(approver_id || '');
            
             // RESET dulu (penting)
    modal.find('#edit_is_active').prop('checked', false);

    // Jika is_active = 1 → checkbox dicentang
    if (parseInt(is_active) === 1) {
        modal.find('#edit_is_active').prop('checked', true);
    }

            // Ganti action form supaya mengarah ke route update
            const updateUrl = `/ruangan/${id}`; // pastikan route update kamu: Route::put('/shift/{id}', ...)
            modal.find('#editRuanganForm').attr('action', updateUrl);

        });
    </script>
@endpush
