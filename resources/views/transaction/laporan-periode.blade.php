@extends('_layouts.app')

@section('title','Laporan Periode Shift')

@section('content')
<div class="container mt-4">

    <h4 class="mb-3">📅 Laporan Shift Berdasarkan Periode</h4>

    <!-- Form Filter -->
    <form method="GET" class="card shadow-sm p-3 mb-4">
        <div class="row">
            <div class="col-md-4">
                <label>Dari Tanggal:</label>
                <input type="date" name="from" class="form-control" value="{{ $from }}">
            </div>
            <div class="col-md-4">
                <label>Sampai Tanggal:</label>
                <input type="date" name="to" class="form-control" value="{{ $to }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100"><i class="fa fa-search"></i> Tampilkan</button>
            </div>
        </div>
    </form>

    @if(isset($shifts) && $shifts->count())
    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('transaction.laporanPeriodePdf', ['from'=>$from, 'to'=>$to]) }}" class="btn btn-danger" target="_blank">
            <i class="fa fa-file-pdf"></i> Export PDF
        </a>
    </div>
    @endif

    <!-- Table -->
    <div class="card shadow-sm">
        <table class="table table-bordered table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Kode Shift</th>
                    <th>Dibuka Oleh</th>
                    <th>Mulai</th>
                    <th>Tutup</th>
                    <th>Saldo Awal</th>
                    <th>Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shifts as $shift)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $shift->session_code }}</td>
                    <td>{{ $shift->openedBy->nama ?? '-' }}</td>
                    <td>{{ $shift->start_time->format('d M Y, H:i') }}</td>
                    <td>{{ $shift->end_time ? $shift->end_time->format('d M Y, H:i') : '-' }}</td>
                    <td>Rp {{ number_format($shift->opening_balance,0,',','.') }}</td>
                    <td>Rp {{ number_format($shift->close_balance,0,',','.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Tidak ada data untuk periode ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
