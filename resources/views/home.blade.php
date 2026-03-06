@extends('_layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

    <div class="container-fluid mt-4">


        <div class="row">

            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: #1E88E5;">
                    <div class="card-body">
                        <h6>Total Ruangan</h6>
                        <h2>{{ $totalRuangan }}</h2>
                        <i class="fa fa-exchange fa-2x float-right"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: #D84315;">
                    <div class="card-body">
                        <h6>Total Barang</h6>
                        <h2>{{ $totalBarang }}</h2>
                        <i class="fa fa-exchange fa-2x float-right"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: #8E24AA;">
                    <div class="card-body">
                        <h6>Total Stok</h6>
                        <h2>{{ $totalStok }}</h2>
                        <i class="fa fa-exchange fa-2x float-right"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: #4E73DF;">
                    <div class="card-body">
                        <h6>Total Barang Rusak</h6>
                        <h2>{{ $barangRusak }}</h2>
                        <i class="fa fa-exchange fa-2x float-right"></i>
                    </div>
                </div>
            </div>
        </div>






      <div class="row mt-3">

    {{-- CHART --}}
    <div class="col-md-8 mb-3">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="mb-3">Data Barang Per Ruangan</h6>

                <div style="height:300px">
                    <canvas id="chartTransaksi"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL STOK MENIPIS --}}
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-white">
                <h6 class="mb-0 text-danger">
                    <i class="fa fa-exclamation-triangle me-1"></i>
                    Stok Menipis
                </h6>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ruangan</th>
                            <th>Barang</th>
                            <th class="text-center">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stokMenipis as $stok)
                        <tr>
                            <td>{{ $stok->ruangan->nama_ruangan }}</td>
                            <td>{{ $stok->barang->nama_barang }}</td>
                            <td class="text-center">
                                <span class="badge bg-danger">
                                    {{ $stok->jumlah }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">
                                Tidak ada stok menipis
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>






    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {

                const ctx = document.getElementById('chartTransaksi');
                if (!ctx) return;

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($stokPerRuangan->pluck('nama_ruangan')) !!},
                        datasets: [{
                            label: 'Jumlah Stok',
                            data: {!! json_encode($stokPerRuangan->pluck('total')) !!},
                            fill: true,
                            backgroundColor: 'rgba(0, 123, 255, 0.25)',
                            borderColor: '#007bff',
                            borderWidth: 2,
                            tension: 0.35,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#007bff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // 🔥 INI KUNCI
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

            });
        </script>


    @endsection


@endsection
