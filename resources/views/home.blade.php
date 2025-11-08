@extends('_layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

    <div class="container-fluid mt-4">


        <div class="row">

            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: #1E88E5;">
                    <div class="card-body">
                        <h6>Total Transaksi Hari Ini</h6>
                        <h2>{{ $totalTransactionsToday }}</h2>
                        <i class="fa fa-exchange fa-2x float-right"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: #D84315;">
                    <div class="card-body">
                        <h6>Total Outstanding (Kasbon)</h6>
                        <h2>Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</h2>
                        <i class="fa fa-money fa-2x float-right"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: #8E24AA;">
                    <div class="card-body">
                        <h6>Total Reseller Aktif</h6>
                        <h2>{{ $totalResellers }}</h2>
                        <i class="fa fa-users fa-2x float-right"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card text-white" style="background: #4E73DF;">
                    <div class="card-body">
                        <h6>Total Akun Pembayaran</h6>
                        <h2>{{ $totalBankAccounts }}</h2>
                        <i class="fa fa-bank fa-2x float-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <strong>🧾 10 Transaksi Terbaru</strong>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-striped table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th>Aplikasi</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTransactions as $trx)
                            <tr>
                                <td>{{ $trx->application->nameaplication }}</td>
                                <td>{{ $trx->coin_qty }}</td>
                                <td>Rp {{ number_format($trx->amount_due, 0, ',', '.') }}</td>
                                <td>
                                    <span
                                        class="badge 
                            @if ($trx->status == 'DONE') badge-success
                            @elseif($trx->status == 'PENDING') badge-warning
                            @else badge-danger @endif
                        ">
                                        {{ $trx->status }}
                                    </span>
                                </td>
                                <td>{{ $trx->created_at->format('d M H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>




        <div class="row">

    <!-- Grafik Transaksi -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4 h-100">
            <div class="card-header bg-light">
                <strong>📊 Grafik Transaksi 7 Hari Terakhir</strong>
            </div>
            <div class="card-body">
                <canvas id="chartTransaksi" height="180"></canvas>
            </div>
        </div>
    </div>

    <!-- Shift Aktif -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4 h-100">
            <div class="card-header bg-light">
                <strong>🕒 Shift yang Aktif</strong>
            </div>
            <div class="card-body p-0">

                <ul class="list-group list-group-flush">

                    @forelse ($activeShift as $shift)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $shift->session_code ?? '-' }}</strong><br>
                                <small class="text-muted">
                                    Dibuka oleh: {{ $shift->openedBy->nama ?? '-' }}
                                </small><br>
                                <small class="text-muted">
                                    Mulai: {{ $shift->start_time->format('d M Y, H:i') }}
                                </small>
                            </div>
                            <span class="badge badge-success px-3 py-2">Aktif</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">
                            Tidak ada shift aktif.
                        </li>
                    @endforelse

                </ul>

            </div>
        </div>
    </div>

</div>





            @section('scripts')
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {

                        const ctx = document.getElementById('chartTransaksi').getContext('2d');

                        const chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: {!! json_encode(array_keys($chartData->toArray())) !!},
                                datasets: [{
                                    label: 'Jumlah Transaksi',
                                    data: {!! json_encode(array_values($chartData->toArray())) !!},
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
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    });
                </script>


            @endsection


        @endsection
