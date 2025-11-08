<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background: #eaeaea; }
    </style>
</head>
<body>

<h3>Laporan Shift Periode</h3>
<p><strong>Dari:</strong> {{ $from }} — <strong>Sampai:</strong> {{ $to }}</p>

<table>
    <thead>
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
        @foreach($shifts as $shift)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $shift->session_code }}</td>
            <td>{{ $shift->openedBy->nama ?? '-' }}</td>
            <td>{{ $shift->start_time }}</td>
            <td>{{ $shift->end_time }}</td>
            <td>Rp {{ number_format($shift->opening_balance,0,',','.') }}</td>
            <td>Rp {{ number_format($shift->close_balance,0,',','.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
