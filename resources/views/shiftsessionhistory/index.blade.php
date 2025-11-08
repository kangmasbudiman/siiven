@extends('_layouts.app')

@section('title', 'Riwayat Shift')

@section('content')
<div class="container-fluid mt-3">

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fa fa-history"></i> Riwayat Shift</h5>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Kode Shift</th>
                        <th>Dibuka Oleh</th>
                        <th>Waktu</th>
                        <th>Saldo Awal</th>
                        <th>Saldo Akhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                        <tr>
                            <td><span class="badge badge-primary">{{ $item->session_code }}</span></td>
                            <td>{{ $item->openedBy->nama ?? '-' }}</td>
                            <td>
                                {{ $item->start_time->format('d M H:i') }} →
                                {{ $item->end_time->format('d M H:i') }}
                            </td>
                            <td>Rp {{ number_format($item->opening_balance,0,',','.') }}</td>
                            <td>Rp {{ number_format($item->close_balance,0,',','.') }}</td>
                            <td>
                                <a href="{{ route('shiftsession.detailshift', $item->id) }}"
                                   class="btn btn-sm btn-info">
                                   <i class="fa fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">
                                Belum ada riwayat shift.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
