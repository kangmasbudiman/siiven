<h4>Laporan Pemasukan &amp; Pengeluaran Gudang Logistik — {{ $label }}</h4>

<table>
    <thead>
        <tr>
            <th colspan="9">A. TRANSAKSI MASUK</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Kode Transaksi</th>
            <th>Item</th>
            <th>Total Qty</th>
            <th>Total Nilai</th>
            <th>Dari</th>
            <th>Ke</th>
            <th>Dicatat Oleh</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transaksis->where('tipe', 'masuk') as $t)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $t->tanggal->format('d-m-Y') }}</td>
                <td>{{ $t->kode }}</td>
                <td>{{ $t->jumlah_item }}</td>
                <td>{{ $t->total_qty }}</td>
                <td>{{ $t->total_nilai }}</td>
                <td>{{ $t->dari }}</td>
                <td>{{ $t->ke }}</td>
                <td>{{ $t->user }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="9">B. TRANSAKSI KELUAR</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Kode Transaksi</th>
            <th>Item</th>
            <th>Total Qty</th>
            <th>Total Nilai</th>
            <th>Dari</th>
            <th>Ke</th>
            <th>Dicatat Oleh</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transaksis->where('tipe', 'keluar') as $t)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $t->tanggal->format('d-m-Y') }}</td>
                <td>{{ $t->kode }}</td>
                <td>{{ $t->jumlah_item }}</td>
                <td>{{ $t->total_qty }}</td>
                <td>{{ $t->total_nilai }}</td>
                <td>{{ $t->dari }}</td>
                <td>{{ $t->ke }}</td>
                <td>{{ $t->user }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th colspan="8">C. REKAP PER BARANG</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Qty Masuk</th>
            <th>Nilai Masuk</th>
            <th>Qty Keluar</th>
            <th>Nilai Keluar</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rekapBarang as $b)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $b->kode_barang }}</td>
                <td>{{ $b->nama_barang }}</td>
                <td>{{ $b->satuan }}</td>
                <td>{{ $b->qty_masuk }}</td>
                <td>{{ $b->nilai_masuk }}</td>
                <td>{{ $b->qty_keluar }}</td>
                <td>{{ $b->nilai_keluar }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
