@extends('_layouts.app')

@section('title', 'Detail Shift')

@section('content')
<div class="container-fluid mt-4">

    <!-- Header -->

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Laporan Shift</h4>
        <a href="{{ route('shiftsession.pdf', $shift->id) }}" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Priview PDF
        </a>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">📝 Detail Shift — {{ $shift->session_code }}</h5>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-4">
                    <p><strong>Dibuka Oleh:</strong><br>{{ $shift->openedBy->nama ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Waktu Mulai:</strong><br>{{ $shift->start_time->format('d M Y, H:i') }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Waktu Tutup:</strong><br>
                        {{ $shift->end_time ? $shift->end_time->format('d M Y, H:i') : '-' }}
                    </p>
                </div>
            </div>

            <hr>

            <!-- Kas Summary -->
            <div class="row text-center">
                <div class="col-md-3">
                    <h6>Saldo Awal</h6>
                    <p class="text-primary">Rp {{ number_format($shift->opening_balance, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-3">
                    <h6>Total Uang Masuk</h6>
                    <p class="text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-3">
                    <h6>Saldo Akhir</h6>
                    <p class="text-info">
                        {{ $shift->closing_balance !== null ? 'Rp ' . number_format($shift->closing_balance, 0, ',', '.') : '-' }}
                    </p>
                </div>
                <div class="col-md-3">
                    <h6>Selisih Kas</h6>
                    <p class="{{ ($selisihKas ?? 0) == 0 ? 'text-success' : 'text-danger' }}">
                        {{ $selisihKas !== null ? 'Rp ' . number_format($selisihKas, 0, ',', '.') : '-' }}
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- Transaksi Table -->
    <div class="card shadow">
        <div class="card-header bg-light">
            <strong>📦 Daftar Transaksi dalam Shift Ini</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Aplikasi</th>
                        <th>Qty</th>
                        <th>Tagihan</th>
                        <th>Dibayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $trx)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $trx->application->nameaplication ?? '-' }}</td>
                            <td>{{ $trx->coin_qty }}</td>
                            <td>Rp {{ number_format($trx->amount_due, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($trx->amount_paid, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($trx->outstanding_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($trx->status == 'DONE')
                                    <span class="badge badge-success">DONE</span>
                                @elseif($trx->status == 'PENDING')
                                    <span class="badge badge-warning">PENDING</span>
                                @else
                                    <span class="badge badge-danger">CANCEL</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @if($transactions->count() == 0)
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada transaksi.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
