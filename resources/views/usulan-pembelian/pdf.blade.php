<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Usulan Pembelian Barang - {{ $usulan->nomor_usulan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 14px; color: #000; }

        .page { padding: 35px 45px; }

        /* Header */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .header-table td { vertical-align: middle; padding: 2px 5px; }
        .logo-cell { width: 90px; text-align: center; }
        .logo-cell img { width: 70px; height: 70px; object-fit: contain; }
        .title-cell { text-align: center; }
        .rs-name { font-size: 35px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .rs-address { font-size: 13px; margin-top: 3px; line-height: 1.6; }
        .accreditation-cell { width: 100px; text-align: center; }
        .accreditation-cell img { width: 90px; }

        hr { border: none; border-top: 2px solid #000; margin: 6px 0; }
        hr.thin { border-top: 1px solid #000; margin: 3px 0; }

        /* Form Title */
        .form-title { text-align: center; font-size: 16px; font-weight: bold; text-transform: uppercase;
                       letter-spacing: 2px; margin: 10px 0 8px; }

        /* Info rows */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 14px; }
        .info-table td { border: 1px solid #000; padding: 5px 8px; }
        .info-table td.label { font-weight: bold; background-color: #f5f5f5; width: 22%; white-space: nowrap; }
        .info-table td.value { width: 28%; }

        /* Main table */
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 5px 7px; font-size: 14px; }
        .main-table thead th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        .main-table tbody td { vertical-align: top; }
        .main-table .no-col { width: 30px; text-align: center; }
        .main-table .qty-col { width: 60px; text-align: center; }
        .main-table .price-col { width: 100px; text-align: right; }
        .main-table .total-col { width: 100px; text-align: right; }
        .main-table .desc-col { }

        /* Total row */
        .total-row td { font-weight: bold; font-size: 14px; }

        /* Keterangan */
        .keterangan-box { border: 1px solid #000; padding: 7px 9px; min-height: 55px; margin-bottom: 10px; }
        .keterangan-label { font-weight: bold; font-size: 14px; margin-bottom: 4px; }
        .keterangan-text { font-size: 14px; line-height: 1.5; font-style: italic; }

        /* Signature section */
        .sig-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .sig-table td { border: 1px solid #000; text-align: center; padding: 6px 4px; vertical-align: top; width: 20%; }
        .sig-label { font-size: 14px; font-weight: bold; margin-bottom: 8px; }
        .sig-name { font-size: 14px; font-weight: bold; border-top: 1px solid #000; padding-top: 3px; margin-top: 4px; }
        .sig-jabatan { font-size: 12px; color: #555; }
        .sig-date { font-size: 11px; margin-bottom: 3px; }

        .qr-img { width: 88px; height: 88px; display: block; margin: 2px auto; }
        .waiting-ttd { color: #aaa; font-size: 12px; font-style: italic; padding: 10px 0; }

        .date-line { text-align: right; font-size: 14px; margin-bottom: 5px; }

        .nomor-text { font-size: 14px; color: #555; margin-bottom: 4px; }
    </style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('logo.jpg') }}" alt="Logo RS">
            </td>
            <td class="title-cell">
                <div class="rs-name">RS. Royal Prima Jambi</div>
                <div class="rs-address">
                    Jl. Raden Wijaya RT 35 Kebun Kopi, Kel. Thehok, Kec. Jambi Selatan, Kota Jambi<br>
                    Telp : 0741-41010, Fax : 0741-43295, Email : royal.prima@gmail.com
                </div>
            </td>
            <td class="accreditation-cell">
                <img src="{{ public_path('akre.png') }}" alt="Akreditasi" style="width:110px;">
            </td>
        </tr>
    </table>

    <hr>
    <hr class="thin">

    {{-- FORM TITLE --}}
    <div class="form-title">Usulan Pembelian Barang</div>
<br><br>
    {{-- INFO --}}
    <table class="info-table">
        <tr>
            <td class="label">NOMOR USULAN</td>
            <td class="value">{{ $usulan->nomor_usulan }}</td>
            <td class="label">UNIT / RUANGAN</td>
            <td class="value">{{ $usulan->ruangan->nama_ruangan ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">TANGGAL PENGAJUAN</td>
            <td class="value">{{ $usulan->tanggal_pengajuan->format('d/m/Y') }}</td>
            <td class="label">PENANGGUNG JAWAB</td>
            <td class="value">{{ $usulan->nama_penanggung_jawab }}</td>
        </tr>
    </table>

    {{-- MAIN TABLE --}}
    <table class="main-table">
        <thead>
            <tr>
                <th class="no-col" rowspan="2">NO</th>
                <th class="desc-col" rowspan="2">Keterangan</th>
                <th colspan="2">Banyaknya</th>
                <th class="total-col" rowspan="2">Jumlah</th>
            </tr>
            <tr>
                <th class="qty-col">Jumlah</th>
                <th class="price-col">Harga Satuan</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @for($i = 0; $i < 10; $i++)
                @php $item = $details[$i] ?? null; @endphp
                <tr>
                    <td class="no-col">{{ $i + 1 }}</td>
                    <td class="desc-col" style="min-height:18px;">{{ $item ? $item['keterangan'] : '' }}</td>
                    <td class="qty-col">{{ $item ? $item['jumlah'] : '' }}</td>
                    <td class="price-col">
                        @if($item && $item['harga_satuan'] > 0)
                            {{ number_format($item['harga_satuan'], 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="total-col">
                        @if($item && $item['harga_satuan'] > 0)
                            @php $sub = $item['jumlah'] * $item['harga_satuan']; $total += $sub; @endphp
                            {{ number_format($sub, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align:right; border:1px solid #000;">TOTAL</td>
                <td class="total-col" style="border:1px solid #000;">
                    Rp. {{ number_format($total, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- KETERANGAN --}}
    <div class="keterangan-box">
        <div class="keterangan-label">KETERANGAN:</div>
        <div class="keterangan-text">{{ $usulan->keterangan ?? '' }}</div>
    </div>

    {{-- DATE --}}
    <div class="date-line">Jambi, {{ $usulan->tanggal_pengajuan->format('d F Y') }}</div>

    {{-- SIGNATURE TABLE (5 kolom) --}}
    @php
        $sigConfig = [
            0 => ['title' => 'Dibuat Oleh',        'jabatan' => '(Ka. Unit)',       'fixed' => true],
            2 => ['title' => 'Dikonfirmasi Oleh',  'jabatan' => '(Direktur)'],
            1 => ['title' => 'Diperiksa Oleh',     'jabatan' => '(Kabag/Kabid)'],
            3 => ['title' => 'Diketahui Oleh',     'jabatan' => '(Ka. Keuangan)'],
            4 => ['title' => 'Disetujui Oleh',     'jabatan' => '(Bendahara)'],
        ];
    @endphp
    <table class="sig-table">
        <tr>
            @foreach($sigConfig as $level => $cfg)
            <td>
                <div class="sig-label">{{ $cfg['title'] }}</div>
                @if(isset($cfg['fixed']))
                    {{-- Dibuat Oleh - QR link ke detail usulan --}}
                    <img class="qr-img" src="{{ $qrPembuat }}" alt="QR">
                    <div class="sig-date" style="font-size:8px;">{{ $usulan->tanggal_pengajuan->format('d/m/Y') }}</div>
                    <div class="sig-name">{{ $usulan->nama_penanggung_jawab }}</div>
                    <div class="sig-jabatan">Ka. Unit - {{ $usulan->ruangan->nama_ruangan ?? '' }}</div>
                @elseif(isset($approvalData[$level]) && $approvalData[$level])
                    @php $ap = $approvalData[$level]; @endphp
                    @if(isset($qrImages[$level]))
                        <img class="qr-img" src="{{ $qrImages[$level] }}" alt="QR">
                    @endif
                    <div class="sig-date" style="font-size:8px;">{{ $ap->approved_at->format('d/m/Y H:i') }}</div>
                    <div class="sig-name">{{ $ap->approver->nama ?? '-' }}</div>
                    <div class="sig-jabatan">{{ $ap->approver->jabatan ?? $cfg['jabatan'] }}</div>
                @else
                    <div class="waiting-ttd">Menunggu...</div>
                    <div class="sig-name">-</div>
                    <div class="sig-jabatan">{{ $cfg['jabatan'] }}</div>
                @endif
            </td>
            @endforeach
        </tr>
    </table>

</div>

{{-- HALAMAN 2: LAMPIRAN FOTO --}}
@if(!empty($lampiranPerItem) || !empty($lampiranBase64))
<div class="page" style="page-break-before: always;">

    {{-- Header ulang --}}
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img src="{{ public_path('logo.jpg') }}" alt="Logo RS">
            </td>
            <td class="title-cell">
                <div class="rs-name">RS. Royal Prima Jambi</div>
                <div class="rs-address">
                    Jl. Raden Wijaya RT 35 Kebun Kopi, Kel. Thehok, Kec. Jambi Selatan, Kota Jambi<br>
                    Telp : 0741-41010, Fax : 0741-43295, Email : royal.prima@gmail.com
                </div>
            </td>
            <td class="accreditation-cell">
                <img src="{{ public_path('akre.png') }}" alt="Akreditasi" style="width:110px;">
            </td>
        </tr>
    </table>

    <hr>
    <hr class="thin">

    <div class="form-title" style="margin-bottom:4px;">Lampiran Foto</div>
    <div style="text-align:center; font-size:13px; color:#555; margin-bottom:16px;">
        {{ $usulan->nomor_usulan }} &mdash; {{ $usulan->ruangan->nama_ruangan ?? '' }}
    </div>

    {{-- Lampiran Per Item --}}
    @foreach($lampiranPerItem as $itemGroup)
    <div style="margin-bottom:16px;">
        <div style="background:#f0f0f0; padding:5px 8px; font-weight:bold; font-size:13px; border-left:4px solid #333; margin-bottom:8px;">
            Item {{ $itemGroup['no_urut'] }}: {{ $itemGroup['keterangan'] }}
        </div>
        <table style="width:100%; border-collapse:collapse;">
            @php $chunks = array_chunk($itemGroup['fotos'], 2); @endphp
            @foreach($chunks as $row)
            <tr>
                @foreach($row as $lmp)
                <td style="width:50%; padding:6px; vertical-align:top; text-align:center;">
                    <img src="{{ $lmp['base64'] }}"
                         style="max-width:100%; max-height:220px; object-fit:contain; border:1px solid #ccc;">
                    <div style="font-size:10px; color:#555; margin-top:3px; word-break:break-all;">
                        {{ $lmp['nama_file'] }}
                    </div>
                </td>
                @endforeach
                @if(count($row) === 1)<td style="width:50%;"></td>@endif
            </tr>
            @endforeach
        </table>
    </div>
    @endforeach

    {{-- Lampiran Umum (jika ada) --}}
    @if(!empty($lampiranBase64))
    <div style="margin-top:16px;">
        <div style="background:#f0f0f0; padding:5px 8px; font-weight:bold; font-size:13px; border-left:4px solid #333; margin-bottom:8px;">
            Lampiran Umum
        </div>
        <table style="width:100%; border-collapse:collapse;">
            @php $chunks = array_chunk($lampiranBase64, 2); @endphp
            @foreach($chunks as $row)
            <tr>
                @foreach($row as $lmp)
                <td style="width:50%; padding:6px; vertical-align:top; text-align:center;">
                    <img src="{{ $lmp['base64'] }}"
                         style="max-width:100%; max-height:220px; object-fit:contain; border:1px solid #ccc;">
                    <div style="font-size:10px; color:#555; margin-top:3px; word-break:break-all;">
                        {{ $lmp['nama_file'] }}
                    </div>
                </td>
                @endforeach
                @if(count($row) === 1)<td style="width:50%;"></td>@endif
            </tr>
            @endforeach
        </table>
    </div>
    @endif

</div>
@endif

</body>
</html>
