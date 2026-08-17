<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemasukan &amp; Pengeluaran Logistik</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
        }

        .header { width: 100%; border-bottom: 2px solid #000; margin-bottom: 10px; }
        .header-table { width: 100%; }
        .header-table td { vertical-align: middle; }
        .logo { width: 70px; }
        .title { text-align: center; }
        .title h2 { margin: 0; font-size: 16px; }
        .title p { margin: 2px 0 0; font-size: 11px; }

        .meta { margin: 10px 0; }
        .meta table { width: 100%; font-size: 11px; }
        .meta td { padding: 2px 0; }

        .ringkasan { width: 100%; border-collapse: collapse; margin: 10px 0; }
        .ringkasan th, .ringkasan td { border: 1px solid #000; padding: 6px; text-align: center; }
        .ringkasan th { background: #f0f0f0; }

        h3.seksi { font-size: 12px; margin: 14px 0 4px; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th, table.data td { border: 1px solid #000; padding: 5px; }
        table.data th { background: #f0f0f0; text-align: center; }
        table.data td { vertical-align: top; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }

        .footer { margin-top: 40px; width: 100%; }
        .signature { width: 30%; text-align: center; float: right; }
        .signature p { margin-bottom: 60px; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td width="15%">
                    <img src="{{ public_path('logo.jpg') }}" class="logo">
                </td>
                <td class="title">
                    <h2>RUMAH SAKIT ROYAL PRIMA JAMBI</h2>
                    <p>Jl. Raden Wijaya RT 35, Kebun Kopi, Kel THehok Kec. Jambi Selatan Kota Jambi</p>
                    <p>Telp. (0741-41010)</p>
                </td>
                <td width="15%"></td>
            </tr>
        </table>
    </div>

    {{-- JUDUL --}}
    <div class="title">
        <h2>LAPORAN PEMASUKAN &amp; PENGELUARAN GUDANG LOGISTIK</h2>
    </div>

    {{-- META INFO --}}
    <div class="meta">
        <table>
            <tr>
                <td width="20%">{{ $label }}</td>
                <td width="2%">:</td>
                <td>{{ $ruangan->kode_ruangan }} - {{ $ruangan->nama_ruangan }}</td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>:</td>
                <td>{{ now()->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Dicetak Oleh</td>
                <td>:</td>
                <td>{{ auth()->user()->nama ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- RINGKASAN --}}
    <table class="ringkasan">
        <tr>
            <th width="34%">Total Nilai Pemasukan</th>
            <th width="34%">Total Nilai Pengeluaran</th>
            <th width="32%">Jumlah Transaksi</th>
        </tr>
        <tr>
            <td class="fw-bold">Rp {{ number_format($nilaiMasuk, 0, ',', '.') }}</td>
            <td class="fw-bold">Rp {{ number_format($nilaiKeluar, 0, ',', '.') }}</td>
            <td class="fw-bold">{{ $jumlahTransaksi }}</td>
        </tr>
    </table>

    {{-- TRANSAKSI MASUK --}}
    <h3 class="seksi">A. TRANSAKSI MASUK</h3>
    <table class="data">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="10%">Tanggal</th>
                <th width="14%">Kode Transaksi</th>
                <th width="6%">Item</th>
                <th width="7%">Total Qty</th>
                <th width="13%">Total Nilai</th>
                <th width="20%">Dari &rarr; Ke</th>
                <th width="12%">Dicatat Oleh</th>
                <th width="14%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $masuk = $transaksis->where('tipe', 'masuk'); @endphp
            @forelse ($masuk as $t)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $t->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $t->kode }}</td>
                    <td class="text-center">{{ $t->jumlah_item }}</td>
                    <td class="text-center">{{ $t->total_qty }}</td>
                    <td class="text-end">Rp {{ number_format($t->total_nilai, 0, ',', '.') }}</td>
                    <td>{{ $t->dari }} &rarr; {{ $t->ke }}</td>
                    <td>{{ $t->user }}</td>
                    <td>{{ $t->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center">Tidak ada transaksi masuk.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- TRANSAKSI KELUAR --}}
    <h3 class="seksi">B. TRANSAKSI KELUAR</h3>
    <table class="data">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="10%">Tanggal</th>
                <th width="14%">Kode Transaksi</th>
                <th width="6%">Item</th>
                <th width="7%">Total Qty</th>
                <th width="13%">Total Nilai</th>
                <th width="20%">Dari &rarr; Ke</th>
                <th width="12%">Dicatat Oleh</th>
                <th width="14%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $keluar = $transaksis->where('tipe', 'keluar'); @endphp
            @forelse ($keluar as $t)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ $t->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $t->kode }}</td>
                    <td class="text-center">{{ $t->jumlah_item }}</td>
                    <td class="text-center">{{ $t->total_qty }}</td>
                    <td class="text-end">Rp {{ number_format($t->total_nilai, 0, ',', '.') }}</td>
                    <td>{{ $t->dari }} &rarr; {{ $t->ke }}</td>
                    <td>{{ $t->user }}</td>
                    <td>{{ $t->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center">Tidak ada transaksi keluar.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- REKAP PER BARANG --}}
    <h3 class="seksi">C. REKAP PER BARANG</h3>
    <table class="data">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="14%">Kode Barang</th>
                <th width="26%">Nama Barang</th>
                <th width="8%">Satuan</th>
                <th width="9%">Qty Masuk</th>
                <th width="13%">Nilai Masuk</th>
                <th width="9%">Qty Keluar</th>
                <th width="13%">Nilai Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekapBarang as $b)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $b->kode_barang }}</td>
                    <td>{{ $b->nama_barang }}</td>
                    <td class="text-center">{{ $b->satuan }}</td>
                    <td class="text-center">{{ $b->qty_masuk }}</td>
                    <td class="text-end">Rp {{ number_format($b->nilai_masuk, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $b->qty_keluar }}</td>
                    <td class="text-end">Rp {{ number_format($b->nilai_keluar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">Tidak ada pergerakan barang.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="signature">
            <p>Mengetahui,</p>
            <strong>Kepala Bagian Umum dan Program</strong>
        </div>
    </div>

</body>
</html>
