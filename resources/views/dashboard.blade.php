@extends('_layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">📊 POS Voucher System</h4>
                </div>
                <div class="card-body text-center">
                    
                    @if($activeShift)
                    <div class="alert alert-success">
                        <h5>🟢 Shift Aktif: {{ $activeShift->shift->name }}</h5>
                        <p>{{ $activeShift->shift_date->format('d F Y') }}</p>
                        
                        <div class="mt-3">
                            <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-lg me-2">
                                💰 Input Transaksi
                            </a>
                            <a href="{{ route('shift.close.form') }}" class="btn btn-warning btn-lg">
                                🔒 Tutup Shift
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <h5>Selamat Datang!</h5>
                        <p>Silakan mulai shift untuk memulai transaksi</p>
                        <a href="{{ route('shift.create') }}" class="btn btn-primary btn-lg">
                            🏁 Mulai Shift
                        </a>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection