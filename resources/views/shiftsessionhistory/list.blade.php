@extends('_layouts.app')

@section('title', 'Riwayat Shift Yang sudah selesai dan belum selesai')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')
<div class="container-fluid mt-4">

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa fa-history"></i> Riwayat Shift</h5>
        </div>

        <div class="card-body">
            <table id="shiftTable" class="table table-bordered table-striped" style="width:100%">
                <thead class="thead-light">
                    <tr>
                        <th>Kode Shift</th>
                        <th>Dibuka Oleh</th>
                        <th>Mulai</th>
                        <th>Tutup</th>
                        <th>Saldo Awal</th>
                        <th>Saldo Akhir</th>
                        <th>Status</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shiftsessions as $shift)
                        <tr>
                            <td><strong>{{ $shift->session_code }}</strong></td>
                            <td>{{ $shift->openedBy->nama ?? '-' }}</td>
                            <td>{{ $shift->start_time->format('d M Y, H:i') }}</td>
                            <td>{{ $shift->end_time ? $shift->end_time->format('d M Y, H:i') : '-' }}</td>
                            <td>Rp {{ number_format($shift->opening_balance, 0, ',', '.') }}</td>
                            <td>
                                {{ $shift->close_balance ? 'Rp '.number_format($shift->close_balance, 0, ',', '.') : '-' }}
                            </td>
                            <td>
                                @if($shift->status == 'ACTIVE')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Tutup</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('shiftsession.report', $shift->id) }}" class="btn btn-sm btn-info">
                                    <i class="fa fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                Tidak ada data shift.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#shiftTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                },
                order: [[2, 'desc']] // default sort by start_time desc
            });
        });
    </script>
@endpush
