<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Mutasi Stok</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>

<h3>Laporan Mutasi Stok</h3>

@if($startDate && $endDate)
<p><strong>Periode:</strong> {{ $startDate }} s/d {{ $endDate }}</p>
@else
<p><strong>Periode:</strong> Semua Data (50 Terbaru)</p>
@endif

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Aplikasi</th>
            <th>Jumlah</th>
            <th>Tipe</th>
            <th>Keterangan</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($logs as $log)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $log->user->nama ?? '-' }}</td>
            <td>{{ $log->aplikasi->nameaplication ?? '-' }}</td>
            <td>{{ number_format($log->amount, 2) }}</td>
            <td>{{ $log->type }}</td>
            <td>{{ $log->note }}</td>
            <td>{{ $log->created_at->format('d M Y H:i') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
