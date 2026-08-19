<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Exports\LaporanLogistikExport;
use App\Models\Barang;
use App\Models\Kategoris;
use App\Models\Kondisis;
use App\Models\Ruangan;
use App\Models\StockBarang;
use App\Models\TransaksiStok;
use App\Services\KodeBarangGenerator;

class LogistikController extends Controller
{
    private $ruanganError;

    /**
     * Stok gudang logistik milik user yang login.
     */
    public function index()
    {
        $ruangan = $this->ruanganLogistik();
        if (!$ruangan) {
            return view('logistik.index', [
                'stoks' => collect(),
                'ruangan' => null,
                'errorRuangan' => $this->ruanganError,
            ]);
        }

        $stoks = StockBarang::with(['barang.kategori', 'kondisi'])
            ->where('ruangan_id', $ruangan->id)
            ->get();

        return view('logistik.index', compact('stoks', 'ruangan'));
    }

    /**
     * Form pemasukan barang (barang masuk ke gudang logistik).
     */
    public function createPemasukan()
    {
        $ruangan = $this->ruanganLogistik();
        if (!$ruangan) {
            return redirect()->route('logistik.index')->with('error', $this->ruanganError);
        }

        $barangs = Barang::where('is_active', 1)->orderBy('nama_barang')->get();
        $kategoris = Kategoris::orderBy('nama_kategori')->get();

        // Harga terakhir di gudang untuk prefill harga satuan
        $hargaMap = StockBarang::where('ruangan_id', $ruangan->id)
            ->whereNotNull('harga')
            ->pluck('harga', 'barang_id');

        return view('logistik.pemasukan', compact('barangs', 'ruangan', 'hargaMap', 'kategoris'));
    }

    /**
     * Shortcut tambah barang baru dari form pemasukan (AJAX, tanpa lewat master data admin).
     */
    public function storeBarangQuick(Request $request)
    {
        $validatedData = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|integer|exists:kategoris,id',
            'jenis_barang' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'spesifikasi' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
        ]);

        $validatedData['kode_barang'] = KodeBarangGenerator::generate();
        $validatedData['is_active'] = 1;

        $barang = Barang::create($validatedData);

        return response()->json([
            'id' => $barang->id,
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama_barang,
            'satuan' => $barang->satuan,
        ]);
    }

    /**
     * Simpan pemasukan barang (multi item): tambah stok gudang + catat ledger.
     */
    public function storePemasukan(Request $request)
    {
        $ruangan = $this->ruanganLogistik();
        if (!$ruangan) {
            return redirect()->back()->with('error', $this->ruanganError);
        }

        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|integer|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|integer|min:0',
        ]);

        $kodeTransaksi = null;

        try {
            DB::transaction(function () use ($validatedData, $ruangan, &$kodeTransaksi) {
                $kondisiBaikId = $this->kondisiBaikId();
                $kodeTransaksi = $this->generateKodeTransaksi('masuk', $validatedData['tanggal']);

                foreach ($validatedData['items'] as $item) {
                    $stok = StockBarang::where([
                        ['barang_id', $item['barang_id']],
                        ['ruangan_id', $ruangan->id],
                    ])->lockForUpdate()->first();

                    if ($stok) {
                        $stok->update([
                            'jumlah' => $stok->jumlah + $item['jumlah'],
                            'harga' => $item['harga_satuan'],
                        ]);
                    } else {
                        StockBarang::create([
                            'barang_id' => $item['barang_id'],
                            'ruangan_id' => $ruangan->id,
                            'jumlah' => $item['jumlah'],
                            'kondisi_id' => $kondisiBaikId,
                            'user_id' => auth()->user()->idUser,
                            'harga' => $item['harga_satuan'],
                            'nomorInventaris' => $this->generateNomorInventaris($item['barang_id'], $ruangan->nama_ruangan),
                            'keterangan' => $validatedData['keterangan'],
                            'tanggalPenerimaan' => $validatedData['tanggal'],
                        ]);
                    }

                    TransaksiStok::create([
                        'tipe' => 'masuk',
                        'kode_transaksi' => $kodeTransaksi,
                        'barang_id' => $item['barang_id'],
                        'ruangan_tujuan_id' => $ruangan->id,
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $item['harga_satuan'],
                        'user_id' => auth()->user()->idUser,
                        'tanggal' => $validatedData['tanggal'],
                        'keterangan' => $validatedData['keterangan'],
                    ]);
                }
            });

            $jumlahItem = count($validatedData['items']);
            $grandTotal = 0;
            foreach ($validatedData['items'] as $item) {
                $grandTotal += $item['jumlah'] * $item['harga_satuan'];
            }

            return redirect()->route('logistik.index')
                ->with('success', 'Pemasukan ' . $jumlahItem . ' item berhasil dicatat (' . $kodeTransaksi . '). Grand total: Rp ' . number_format($grandTotal, 0, ',', '.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Form pengeluaran barang (distribusi ke ruangan).
     */
    public function createPengeluaran()
    {
        $ruangan = $this->ruanganLogistik();
        if (!$ruangan) {
            return redirect()->route('logistik.index')->with('error', $this->ruanganError);
        }

        $stoks = StockBarang::with('barang')
            ->where('ruangan_id', $ruangan->id)
            ->where('jumlah', '>', 0)
            ->get();

        $ruangans = Ruangan::where('is_active', '1')
            ->where('id', '!=', $ruangan->id)
            ->orderBy('nama_ruangan')
            ->get();

        return view('logistik.pengeluaran', compact('stoks', 'ruangans', 'ruangan'));
    }

    /**
     * Simpan pengeluaran barang (multi item): kurangi stok gudang, tambah
     * stok ruangan tujuan, catat ledger. Semua dalam satu transaksi DB.
     */
    public function storePengeluaran(Request $request)
    {
        $ruangan = $this->ruanganLogistik();
        if (!$ruangan) {
            return redirect()->back()->with('error', $this->ruanganError);
        }

        $validatedData = $request->validate([
            'ruangan_tujuan_id' => 'required|integer|exists:ruangans,id',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|integer|exists:barangs,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        if ($validatedData['ruangan_tujuan_id'] == $ruangan->id) {
            return redirect()->back()
                ->with('error', 'Ruangan tujuan tidak valid (sama dengan gudang logistik).')
                ->withInput();
        }

        $kodeTransaksi = null;

        try {
            DB::transaction(function () use ($validatedData, $ruangan, &$kodeTransaksi) {
                $kondisiBaikId = $this->kondisiBaikId();
                $ruanganTujuan = Ruangan::findOrFail($validatedData['ruangan_tujuan_id']);
                $kodeTransaksi = $this->generateKodeTransaksi('keluar', $validatedData['tanggal']);
                $grandTotal = 0;

                foreach ($validatedData['items'] as $item) {
                    $stok = StockBarang::where([
                        ['barang_id', $item['barang_id']],
                        ['ruangan_id', $ruangan->id],
                    ])->lockForUpdate()->first();

                    if (!$stok || $stok->jumlah < $item['jumlah']) {
                        $namaBarang = $stok ? $stok->barang->nama_barang : Barang::find($item['barang_id'])->nama_barang;
                        $tersedia = $stok ? $stok->jumlah : 0;
                        throw ValidationException::withMessages([
                            'items' => 'Stok ' . $namaBarang . ' tidak cukup. Tersedia: ' . $tersedia,
                        ]);
                    }

                    $stok->update(['jumlah' => $stok->jumlah - $item['jumlah']]);

                    $stokTujuan = StockBarang::where([
                        ['barang_id', $item['barang_id']],
                        ['ruangan_id', $ruanganTujuan->id],
                    ])->lockForUpdate()->first();

                    if ($stokTujuan) {
                        $stokTujuan->increment('jumlah', $item['jumlah']);
                    } else {
                        StockBarang::create([
                            'barang_id' => $item['barang_id'],
                            'ruangan_id' => $ruanganTujuan->id,
                            'jumlah' => $item['jumlah'],
                            'kondisi_id' => $kondisiBaikId,
                            'user_id' => auth()->user()->idUser,
                            'harga' => $stok->harga,
                            'nomorInventaris' => $this->generateNomorInventaris($item['barang_id'], $ruanganTujuan->nama_ruangan),
                            'keterangan' => $validatedData['keterangan'],
                            'tanggalPenerimaan' => $validatedData['tanggal'],
                        ]);
                    }

                    TransaksiStok::create([
                        'tipe' => 'keluar',
                        'kode_transaksi' => $kodeTransaksi,
                        'barang_id' => $item['barang_id'],
                        'ruangan_asal_id' => $ruangan->id,
                        'ruangan_tujuan_id' => $ruanganTujuan->id,
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $stok->harga,
                        'user_id' => auth()->user()->idUser,
                        'tanggal' => $validatedData['tanggal'],
                        'keterangan' => $validatedData['keterangan'],
                    ]);

                    $grandTotal += $item['jumlah'] * (int) $stok->harga;
                }
            });

            $jumlahItem = count($validatedData['items']);

            return redirect()->route('logistik.index')
                ->with('success', 'Pengeluaran ' . $jumlahItem . ' item berhasil dicatat (' . $kodeTransaksi . ')!');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Riwayat transaksi masuk/keluar gudang logistik, ringkas per transaksi.
     */
    public function riwayat(Request $request)
    {
        $ruangan = $this->ruanganLogistik();
        if (!$ruangan) {
            return redirect()->route('logistik.index')->with('error', $this->ruanganError);
        }

        $query = TransaksiStok::with(['barang', 'ruanganAsal', 'ruanganTujuan', 'user'])
            ->where(function ($q) use ($ruangan) {
                $q->where('ruangan_tujuan_id', $ruangan->id)
                    ->orWhere('ruangan_asal_id', $ruangan->id);
            });

        if ($request->filled('tipe') && in_array($request->tipe, ['masuk', 'keluar'])) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('barang_id')) {
            $query->where('barang_id', $request->barang_id);
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $rows = $query->orderByDesc('tanggal')->orderByDesc('id')->get();
        $groups = $this->ringkasanTransaksi($rows);

        $page = (int) $request->input('page', 1);
        $perPage = 15;
        $transaksis = new LengthAwarePaginator(
            $groups->slice(($page - 1) * $perPage, $perPage)->values(),
            $groups->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        $barangs = Barang::where('is_active', 1)->orderBy('nama_barang')->get();

        return view('logistik.riwayat', compact('transaksis', 'barangs', 'ruangan'));
    }

    /**
     * Detail item satu transaksi (AJAX JSON) untuk modal riwayat.
     */
    public function riwayatDetail($kode)
    {
        $ruangan = $this->ruanganLogistik();
        if (!$ruangan) {
            return response()->json(['error' => $this->ruanganError], 403);
        }

        $query = TransaksiStok::with(['barang', 'ruanganAsal', 'ruanganTujuan', 'user'])
            ->where(function ($q) use ($ruangan) {
                $q->where('ruangan_tujuan_id', $ruangan->id)
                    ->orWhere('ruangan_asal_id', $ruangan->id);
            });

        if (strpos($kode, 'ROW-') === 0) {
            $query->where('id', (int) substr($kode, 4));
        } else {
            $query->where('kode_transaksi', $kode);
        }

        $rows = $query->orderBy('id')->get();
        $first = $rows->first();

        return response()->json([
            'kode' => $kode,
            'tipe' => $first ? $first->tipe : null,
            'tanggal' => $first ? $first->tanggal->format('d-m-Y') : null,
            'keterangan' => $first ? $first->keterangan : null,
            'items' => $rows->map(function ($r) {
                return [
                    'kode_barang' => $r->barang ? $r->barang->kode_barang : '-',
                    'nama_barang' => $r->barang ? $r->barang->nama_barang : '-',
                    'satuan' => $r->barang ? $r->barang->satuan : '-',
                    'jumlah' => $r->jumlah,
                    'harga_satuan' => $r->harga_satuan,
                    'subtotal' => $r->jumlah * ($r->harga_satuan ?? 0),
                ];
            }),
        ]);
    }

    /**
     * Laporan pemasukan/pengeluaran per hari atau per bulan.
     */
    public function laporan(Request $request)
    {
        $ruangan = $this->ruanganLogistik();
        if (!$ruangan) {
            return redirect()->route('logistik.index')->with('error', $this->ruanganError);
        }

        $data = $this->dataLaporan($request, $ruangan);

        return view('logistik.laporan', array_merge($data, [
            'ruangan' => $ruangan,
            'tanggal' => $request->input('tanggal', date('Y-m-d')),
            'bulan' => (int) $request->input('bulan', date('n')),
            'tahun' => (int) $request->input('tahun', date('Y')),
        ]));
    }

    public function laporanPdf(Request $request)
    {
        $ruangan = $this->ruanganLogistik();
        if (!$ruangan) {
            return redirect()->route('logistik.index')->with('error', $this->ruanganError);
        }

        $data = $this->dataLaporan($request, $ruangan);

        $pdf = \PDF::loadView('logistik.laporan_pdf', array_merge($data, ['ruangan' => $ruangan]))
            ->setPaper('A4', 'landscape');

        return $pdf->stream('laporan-logistik-' . $data['filePeriode'] . '.pdf');
    }

    public function laporanExcel(Request $request)
    {
        $ruangan = $this->ruanganLogistik();
        if (!$ruangan) {
            return redirect()->route('logistik.index')->with('error', $this->ruanganError);
        }

        $data = $this->dataLaporan($request, $ruangan);

        return \Excel::download(
            new LaporanLogistikExport($data['transaksis'], $data['rekapBarang'], $data['label']),
            'laporan-logistik-' . $data['filePeriode'] . '.xlsx'
        );
    }

    /**
     * Kumpulkan data laporan sesuai filter periode (harian/bulanan).
     */
    private function dataLaporan(Request $request, $ruangan)
    {
        $periode = $request->input('periode') === 'bulanan' ? 'bulanan' : 'harian';
        $bulanNama = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
            'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $query = TransaksiStok::with(['barang', 'ruanganAsal', 'ruanganTujuan', 'user'])
            ->where(function ($q) use ($ruangan) {
                $q->where('ruangan_tujuan_id', $ruangan->id)
                    ->orWhere('ruangan_asal_id', $ruangan->id);
            });

        if ($periode === 'bulanan') {
            $bulan = min(12, max(1, (int) $request->input('bulan', date('n'))));
            $tahun = (int) $request->input('tahun', date('Y'));
            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            $label = 'Periode: ' . $bulanNama[$bulan] . ' ' . $tahun;
            $filePeriode = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
        } else {
            $tanggal = $request->input('tanggal', date('Y-m-d'));
            $query->whereDate('tanggal', $tanggal);
            $label = 'Periode: ' . date('d-m-Y', strtotime($tanggal));
            $filePeriode = $tanggal;
        }

        $rows = $query->orderByDesc('tanggal')->orderByDesc('id')->get();

        $transaksis = $this->ringkasanTransaksi($rows);
        $rekapBarang = $this->rekapBarang($rows);

        $nilaiMasuk = $rows->where('tipe', 'masuk')->sum(function ($r) {
            return $r->jumlah * ($r->harga_satuan ?? 0);
        });
        $nilaiKeluar = $rows->where('tipe', 'keluar')->sum(function ($r) {
            return $r->jumlah * ($r->harga_satuan ?? 0);
        });

        return [
            'periode' => $periode,
            'label' => $label,
            'filePeriode' => $filePeriode,
            'transaksis' => $transaksis,
            'rekapBarang' => $rekapBarang,
            'nilaiMasuk' => $nilaiMasuk,
            'nilaiKeluar' => $nilaiKeluar,
            'jumlahTransaksi' => $transaksis->count(),
        ];
    }

    /**
     * Grup baris ledger jadi ringkasan per transaksi (kode_transaksi,
     * fallback ROW-{id} untuk baris lama tanpa kode).
     */
    private function ringkasanTransaksi($rows)
    {
        return $rows->groupBy(function ($row) {
            return $row->kode_transaksi ?: 'ROW-' . $row->id;
        })->map(function ($items, $kode) {
            $first = $items->first();
            return (object) [
                'kode' => $kode,
                'tipe' => $first->tipe,
                'tanggal' => $first->tanggal,
                'jumlah_item' => $items->count(),
                'total_qty' => $items->sum('jumlah'),
                'total_nilai' => $items->sum(function ($i) {
                    return $i->jumlah * ($i->harga_satuan ?? 0);
                }),
                'dari' => $first->ruanganAsal ? $first->ruanganAsal->nama_ruangan : '-',
                'ke' => $first->ruanganTujuan ? $first->ruanganTujuan->nama_ruangan : '-',
                'user' => $first->user ? $first->user->nama : '-',
                'keterangan' => $first->keterangan,
                'max_id' => $items->max('id'),
            ];
        })->sortByDesc(function ($g) {
            return $g->tanggal->format('Ymd') . str_pad($g->max_id, 10, '0', STR_PAD_LEFT);
        })->values();
    }

    /**
     * Rekap qty & nilai masuk/keluar per barang.
     */
    private function rekapBarang($rows)
    {
        return $rows->groupBy('barang_id')->map(function ($items) {
            $b = $items->first()->barang;
            $masuk = $items->where('tipe', 'masuk');
            $keluar = $items->where('tipe', 'keluar');
            return (object) [
                'kode_barang' => $b ? $b->kode_barang : '-',
                'nama_barang' => $b ? $b->nama_barang : '-',
                'satuan' => $b ? $b->satuan : '-',
                'qty_masuk' => $masuk->sum('jumlah'),
                'nilai_masuk' => $masuk->sum(function ($i) {
                    return $i->jumlah * ($i->harga_satuan ?? 0);
                }),
                'qty_keluar' => $keluar->sum('jumlah'),
                'nilai_keluar' => $keluar->sum(function ($i) {
                    return $i->jumlah * ($i->harga_satuan ?? 0);
                }),
            ];
        })->sortBy('nama_barang')->values();
    }

    /**
     * Ruangan logistik milik user yang login, null bila belum terhubung
     * atau ruangannya bukan jenis logistik.
     */
    private function ruanganLogistik()
    {
        $ruangan = auth()->user()->ruangan;

        if (!$ruangan) {
            $this->ruanganError = 'Akun Anda belum terhubung ke ruangan. Hubungi Admin untuk mengatur ruangan Logistik.';
            return null;
        }

        if ($ruangan->jenis_ruangan !== 'logistik') {
            $this->ruanganError = 'Ruangan Anda (' . $ruangan->nama_ruangan . ') bukan ruangan jenis Logistik. Hubungi Admin.';
            return null;
        }

        return $ruangan;
    }

    private function kondisiBaikId()
    {
        return Kondisis::where('nama_kondisi', 'Baik')->firstOrFail()->id;
    }

    /**
     * Kode transaksi unik per submission: IN/OUT-yymmdd-0001.
     * Wajib dipanggil di dalam DB::transaction (pakai lockForUpdate).
     */
    private function generateKodeTransaksi($tipe, $tanggal)
    {
        $prefix = ($tipe === 'masuk' ? 'IN-' : 'OUT-') . date('ymd', strtotime($tanggal)) . '-';
        $seq = TransaksiStok::where('kode_transaksi', 'like', $prefix . '%')
            ->lockForUpdate()
            ->distinct()
            ->count('kode_transaksi') + 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    private function generateNomorInventaris($barangId, $namaRuangan)
    {
        return 'RPJ/' . $barangId . '/' . $namaRuangan . '/' . date('YmdHis');
    }
}
