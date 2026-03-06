@extends('_layouts.app')

@section('title', 'Dashboard Admin')

@section('content')



    <div class="container-fluid mt-3">

        {{-- ============================== --}}
        {{-- INFORMASI SHIFT PENGGUNA --}}
        {{-- ============================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">👤 Informasi Pengguna</h5>
                <table class="table table-bordered mb-0">
                    <tr>
                        <th>Nama</th>
                        <td>{{ $user_shift->user->nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi</th>
                        <td>{{ $ruangan ?? 'N/A' }}</td>
                    </tr>
                  
              
                </table>
            </div>
        </div>


        <div class="row">
    <div class="col-md-3">
        <div class="card bg-primary border-primary">
            <div class="card-body">
                <h6>Total Jenis Barang</h6>
                <h3>{{ $totalJenisBarang }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success border-success">
            <div class="card-body">
                <h6 class="text-white">Total Stok</h6>
                <h3 class="text-white">{{ $totalStok }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-danger border-danger">
            <div class="card-body">
                <h6 class="text-white">Barang Rusak</h6>
                <h3 class="text-white">{{ $totalRusak }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning border-warning">
            <div class="card-body">
                <h6>Stok Menipis</h6>
                <h3>{{ $stokMenipis }}</h3>
            </div>
        </div>
    </div>
</div>

<hr>

<h5 class="mt-4">Stok Terakhir Diperbarui</h5>
<div class="card shadow-sm mt-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="fa fa-boxes me-2 text-primary"></i>
            Stok Terakhir Diperbarui
        </h6>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Barang</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-end">Update Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stokTerbaru as $stok)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $stok->barang->nama_barang }}</div>
                            <small class="text-muted">
                                Kode: {{ $stok->barang->kode_barang ?? '-' }}
                            </small>
                        </td>

                        <td class="text-center">
                            @if($stok->jumlah <= 3)
                                <span class="badge bg-danger">
                                    {{ $stok->jumlah }}
                                </span>
                            @elseif($stok->jumlah <= 5)
                                <span class="badge bg-warning text-dark">
                                    {{ $stok->jumlah }}
                                </span>
                            @else
                                <span class="badge bg-success">
                                    {{ $stok->jumlah }}
                                </span>
                            @endif
                        </td>

                        <td class="text-end">
                            <small class="text-muted">
                                {{ $stok->updated_at->diffForHumans() }}
                            </small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">
                            <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada data stok
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>








    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const leaveForms = document.querySelectorAll('form[action*="shiftsession.leave"]');
            leaveForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Keluar dari Shift?',
                        text: "Apakah kamu yakin ingin keluar dari shift aktif saat ini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Keluar',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>





@endsection










@section('scripts')
    <script>
        document.querySelectorAll('form[action*="shiftsession.rejoin"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Gabung Kembali ke Shift?',
                    text: 'Apakah kamu yakin ingin kembali ke shift sebelumnya?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Gabung Kembali',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>



@endsection






@section('scripts')
    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: {!! json_encode(session('success')) !!},
                icon: 'success',
                confirmButtonText: 'Oke'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Gagal!',
                text: {!! json_encode(session('error')) !!},
                icon: 'error',
                confirmButtonText: 'Tutup'
            });
        </script>
    @endif







@endsection
