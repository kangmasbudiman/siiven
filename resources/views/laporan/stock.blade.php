<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
        }

        .header-table {
            width: 100%;
        }

        .header-table td {
            vertical-align: middle;
        }

        .logo {
            width: 70px;
        }

        .title {
            text-align: center;
        }

        .title h2 {
            margin: 0;
            font-size: 16px;
        }

        .title p {
            margin: 2px 0 0;
            font-size: 11px;
        }

        .meta {
            margin: 10px 0;
        }

        .meta table {
            width: 100%;
            font-size: 11px;
        }

        .meta td {
            padding: 2px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 6px;
        }

        table.data th {
            background: #f0f0f0;
            text-align: center;
        }

        table.data td {
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
        }

        .signature {
            width: 30%;
            text-align: center;
            float: right;
        }

        .signature p {
            margin-bottom: 60px;
        }

    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <table class="header-table">
        <tr>
            <td width="15%">
                {{-- LOGO RS --}}
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
    <h2>LAPORAN STOK BARANG</h2>
</div>

{{-- META INFO --}}
<div class="meta">
    <table>
        <tr>
            <td width="20%">Tanggal Cetak</td>
            <td width="2%">:</td>
            <td>{{ now()->format('d F Y') }}</td>
        </tr>
        <tr>
            <td>Dicetak Oleh</td>
            <td>:</td>
            <td>{{ auth()->user()->nama ?? '-' }}</td>
        </tr>
    </table>
</div>

{{-- TABEL DATA --}}
<table class="data">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="20%">Ruangan</th>

            <th width="25%">Barang</th>
            <th width="15%">Nomor Inventaris</th>
            <th width="15%">Tanggal Pembelian</th>
            <th width="15%">Tanggal Penerimaan</th>
            <th width="15%">Nomor Seri</th>

            <th width="15%">Kondisi</th>
            <th width="15%">Kategori</th>

            <th width="10%">Jumlah</th>
            <th width="10%">Estimasi Harga</th>
            <th width="10%">Satuan</th>

            <th width="15%">Update</th>
        </tr>
    </thead>
   <tbody>

@php
    $noGlobal = 1;
@endphp

@foreach($groupedData as $namaRuangan => $items)

    {{-- 🔹 HEADER RUANGAN --}}
    <tr>
        <td colspan="13"
            style="font-weight:bold; background:#eaeaea; padding:8px;">
            RUANGAN : {{ strtoupper($namaRuangan) }}
        </td>
    </tr>

    @php
        $subtotal = 0;
    @endphp

    @foreach($items as $row)
        <tr>
            <td class="text-center">{{ $noGlobal++ }}</td>
            <td>{{ $row->ruangan->nama_ruangan }}</td>
            <td>{{ $row->barang->nama_barang }}<br>
                {{ $row->merk ?? '-' }}<br>
                {{ $row->type ?? '-' }}<br>
                {{ $row->nomerSeri ?? '-' }}

            </td>
            <td class="text-center">{{ $row->nomorInventaris }}</td>
            <td class="text-center">{{ $row->tanggalPembelian }}</td>
            <td class="text-center">{{ $row->tanggalPenerimaan }}</td>
            <td class="text-center">{{ $row->nomorSeri }}</td>
            <td class="text-center">{{ $row->kondisi->nama_kondisi }}</td>
            <td class="text-center">
                {{ $row->barang->kategori->nama_kategori ?? '-' }}
            </td>
            <td class="text-center">{{ $row->jumlah }}</td>
            <td class="text-center">Rp {{ number_format($row->harga, 0, ',', '.') }}</td>
            <td class="text-center">{{ $row->barang->satuan }}</td>
            <td class="text-center">
                {{ $row->updated_at->format('d/m/Y') }}
            </td>
        </tr>

        @php
            $subtotal += $row->jumlah;
        @endphp
    @endforeach

    {{-- 🔸 SUBTOTAL PER RUANGAN --}}
    <tr>
        <td colspan="10"
            style="text-align:right; font-weight:bold;">
            Subtotal {{ $namaRuangan }}
        </td>
        <td class="text-center" style="font-weight:bold;">
            {{ $subtotal }}
        </td>
        <td colspan="2"></td>
    </tr>

@endforeach
<tr>
    <td colspan="10"
        style="text-align:right; font-weight:bold;">
        TOTAL KESELURUHAN
    </td>
    <td class="text-center" style="font-weight:bold;">
        {{ $groupedData->flatten()->sum('jumlah') }}
    </td>
    <td colspan="2"></td>
</tr>

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
