@extends('_layouts.app')

@section('title', 'Detail Shift')

@section('content')

<div class="container-fluid mt-3">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="fa fa-clock"></i> Detail Shift — {{ $shift->session_code }}
        </h4>
        <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="row">

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Saldo Awal</h6>
                    <h4 class="font-weight-bold">Rp {{ number_format($shift->opening_balance,0,',','.') }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Masuk (Paid)</h6>
                    <h4 class="font-weight-bold">Rp {{ number_format($totalPaid,0,',','.') }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-left-warning">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Kasbon</h6>
                    <h4 class="font-weight-bold text-warning">Rp {{ number_format($kasbon,0,',','.') }}</h4>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-left-danger">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Selisih Kas</h6>
                    <h4 class="font-weight-bold text-{{ $selisihKas == 0 ? 'success' : 'danger' }}">
                        Rp {{ number_format($selisihKas,0,',','.') }}
                    </h4>
                      @if($selisihKas > 0)
            <small class="text-danger"> → Uang kurang</small>
        @elseif($selisihKas < 0)
            <small class="text-warning"> → Uang lebih</small>
        @else
            <small class="text-success"> → Pas ✅</small>
        @endif
                </div>
            </div>
        </div>




    </div>




    {{-- SHIFT MEMBER --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-3"><i class="fa fa-users"></i> Anggota Shift</h5>
            @forelse($shift->members as $member)
                <span class="badge badge-info p-2 mr-1">{{ $member->user->nama }}</span>
            @empty
                <p class="text-muted mb-0">Belum ada yang bergabung.</p>
            @endforelse
        </div>
    </div>

    {{-- TRANSACTIONS TABLE --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fa fa-receipt"></i> Riwayat Transaksi Shift</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-striped table-hover table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Waktu</th>
                        <th>Customer</th>
                        <th>Aplikasi</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $trx)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $trx->created_at->format('H:i') }}</td>
                            <td>{{ $trx->customer_type }}</td>
                            <td>{{ $trx->application->nameaplication }}</td>
                            <td>{{ $trx->coin_qty }}</td>
                            <td>Rp {{ number_format($trx->amount_due,0,',','.') }}</td>
                            <td>Rp {{ number_format($trx->amount_paid,0,',','.') }}</td>
                            <td>Rp {{ number_format($trx->outstanding_amount,0,',','.') }}</td>
                            <td><span class="badge badge-{{ $trx->status == 'DONE' ? 'success' : 'warning' }}">{{ $trx->status }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
