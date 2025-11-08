<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Shift - {{ $shift->session_code }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2, h3 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #666; padding: 6px 8px; text-align: left; }
        th { background: #f0f0f0; }
        .summary-table td { border: none; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <h2>Laporan Shift</h2>
    <p><strong>Kode Shift:</strong> {{ $shift->session_code }}</p>

    <hr>

    <table class="summary-table">
        <tr>
            <td><strong>Dibuka Oleh</strong></td>
            <td>: {{ $shift->openedBy->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Waktu Mulai</strong></td>
            <td>: {{ $shift->start_time->format('d M Y, H:i') }}</td>
        </tr>
        <tr>
            <td><strong>Waktu Tutup</strong></td>
            <td>: {{ $shift->end_time ? $shift->end_time->format('d M Y, H:i') : '-' }}</td>
        </tr>
        <tr>
            <td><strong>Saldo Awal</strong></td>
            <td>: Rp {{ number_format($shift->opening_balance, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Total Uang Masuk</strong></td>
            <td>: Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Saldo Akhir</strong></td>
            <td>: {{ $shift->close_balance !== null ? 'Rp '.number_format($shift->close_balance, 0, ',', '.') : '-' }}</td>
        </tr>
        <tr>
            <td><strong>Selisih Kas</strong></td>
            <td>: {{ $selisihKas !== null ? 'Rp '.number_format($selisihKas, 0, ',', '.') : '-' }}</td>
        </tr>
    </table>

    <br><br>
    <h3>Daftar Transaksi</h3>

    <table>
        <thead>
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
                <td class="text-right">Rp {{ number_format($trx->amount_due, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($trx->amount_paid, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($trx->outstanding_amount, 0, ',', '.') }}</td>
                <td>{{ $trx->status }}</td>
            </tr>
            @endforeach

            @if($transactions->count() == 0)
                <tr>
                    <td colspan="7" class="text-center">Belum ada transaksi.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <br><br>

    <p><small>Dicetak pada: {{ now()->format('d M Y, H:i') }}</small></p>

</body>
</html>
