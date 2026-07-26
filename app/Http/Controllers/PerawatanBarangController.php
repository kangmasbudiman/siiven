<?php

namespace App\Http\Controllers;

use App\Models\PerawatanBarang;
use App\Models\Ruangan;
use App\Models\StockBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PerawatanBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = PerawatanBarang::with([
            'stockBarang.barang',
            'stockBarang.ruangan',
            'teknisi',
        ])->latestFirst();

        if ($request->filled('dari')) {
            $query->where('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('tanggal', '<=', $request->sampai);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_perawatan', $request->jenis);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('ruangan_id')) {
            $query->whereHas('stockBarang', function ($q) use ($request) {
                $q->where('ruangan_id', $request->ruangan_id);
            });
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('stockBarang.barang', function ($sub) use ($q) {
                $sub->where('nama_barang', 'like', "%{$q}%")
                    ->orWhere('kode_barang', 'like', "%{$q}%");
            });
        }

        $riwayat = $query->paginate(25)->appends($request->query());
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();

        return view('perawatan.index', compact('riwayat', 'ruangans'));
    }

    public function scan(Request $request)
    {
        $code = trim($request->query('code', ''));

        // Auto-lookup jika ada parameter ?code= dari URL (mis. hasil scan HP)
        if ($code !== '') {
            $stock = StockBarang::with(['barang', 'ruangan'])
                ->where('nomorInventaris', $code)
                ->first();

            if ($stock) {
                return redirect()->route('perawatan.create', $stock->id);
            }

            return redirect()->route('perawatan.scan')
                ->with('error', "Nomor inventaris \"{$code}\" tidak ditemukan.");
        }

        return view('perawatan.scan');
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = trim($request->input('code'));

        $stock = StockBarang::with(['barang', 'ruangan'])
            ->where('nomorInventaris', $code)
            ->first();

        if (!$stock) {
            return redirect()->route('perawatan.scan')
                ->with('error', "Nomor inventaris \"{$code}\" tidak ditemukan.")
                ->withInput();
        }

        return redirect()->route('perawatan.create', $stock->id);
    }

    public function create($stockId)
    {
        $stock = StockBarang::with(['barang', 'ruangan', 'kondisi'])
            ->findOrFail($stockId);

        $riwayat = PerawatanBarang::with('teknisi')
            ->byStock($stockId)
            ->latestFirst()
            ->limit(10)
            ->get();

        return view('perawatan.create', [
            'stock'   => $stock,
            'riwayat' => $riwayat,
        ]);
    }

    public function store(Request $request, $stockId)
    {
        $stock = StockBarang::findOrFail($stockId);

        $validated = $request->validate([
            'tanggal'         => 'required|date',
            'jam'             => 'required|date_format:H:i',
            'jenis_perawatan' => 'required|in:pembersihan,preventive,corrective,kalibrasi,lainnya',
            'status'          => 'required|in:selesai,pending,bermasalah',
            'keterangan'      => 'nullable|string|max:2000',
            'foto_sebelum'    => 'nullable|image|max:5120',
            'foto_sesudah'    => 'nullable|image|max:5120',
            'ttd_teknisi'     => 'required|string',
        ], [
            'ttd_teknisi.required' => 'Tanda tangan teknisi wajib diisi.',
            'foto_sebelum.image'   => 'Foto sebelum harus berupa gambar.',
            'foto_sebelum.max'     => 'Ukuran foto sebelum maksimal 5MB.',
            'foto_sesudah.image'   => 'Foto sesudah harus berupa gambar.',
            'foto_sesudah.max'     => 'Ukuran foto sesudah maksimal 5MB.',
        ]);

        $data = [
            'stock_barang_id' => $stock->id,
            'teknisi_id'      => Auth::user()->idUser,
            'tanggal'         => $validated['tanggal'],
            'jam'             => $validated['jam'],
            'jenis_perawatan' => $validated['jenis_perawatan'],
            'status'          => $validated['status'],
            'keterangan'      => $validated['keterangan'] ?? null,
            'ttd_teknisi'     => $this->sanitizeSignature($validated['ttd_teknisi']),
        ];

        if ($request->hasFile('foto_sebelum')) {
            $data['foto_sebelum'] = $request->file('foto_sebelum')
                ->store('perawatan', 'public');
        }
        if ($request->hasFile('foto_sesudah')) {
            $data['foto_sesudah'] = $request->file('foto_sesudah')
                ->store('perawatan', 'public');
        }

        $perawatan = PerawatanBarang::create($data);

        return redirect()
            ->route('perawatan.show', $perawatan->id)
            ->with('success', 'Catatan perawatan berhasil disimpan.');
    }

    public function history(Request $request, $stockId)
    {
        $stock = StockBarang::with(['barang', 'ruangan'])->findOrFail($stockId);

        $query = PerawatanBarang::with('teknisi')
            ->byStock($stockId)
            ->latestFirst();

        if ($request->filled('dari')) {
            $query->where('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->where('tanggal', '<=', $request->sampai);
        }
        if ($request->filled('jenis')) {
            $query->where('jenis_perawatan', $request->jenis);
        }

        $riwayat = $query->paginate(20)->appends($request->query());

        return view('perawatan.history', compact('stock', 'riwayat'));
    }

    public function show($id)
    {
        $perawatan = PerawatanBarang::with(['stockBarang.barang', 'stockBarang.ruangan', 'teknisi'])
            ->findOrFail($id);

        return view('perawatan.show', compact('perawatan'));
    }

    public function printBarcode($stockId)
    {
        $stock = StockBarang::with(['barang', 'ruangan'])->findOrFail($stockId);

        if (empty(trim((string)$stock->nomorInventaris))) {
            return redirect()
                ->route('perawatan.barcode.batch')
                ->with('error', 'Item ini belum memiliki nomor inventaris. Isi dulu di data inventory sebelum mencetak barcode.');
        }

        return view('perawatan.print_barcode', compact('stock'));
    }

    public function printBarcodeBatch(Request $request)
    {
        $ruanganId = $request->input('ruangan_id');

        $query = StockBarang::with(['barang', 'ruangan'])
            ->whereNotNull('nomorInventaris')
            ->where('nomorInventaris', '!=', '');
        if ($ruanganId) {
            $query->where('ruangan_id', $ruanganId);
        }
        $stocks = $query->orderBy('ruangan_id')->get();

        $ruangans = Ruangan::orderBy('nama_ruangan')->get();

        return view('perawatan.print_barcode_batch', compact('stocks', 'ruangans', 'ruanganId'));
    }

    /**
     * Strip prefix data:image/png;base64, dari signature pad supaya kolom DB berisi base64 murni.
     */
    private function sanitizeSignature(string $data): string
    {
        if (Str::startsWith($data, 'data:image')) {
            return substr($data, strpos($data, ',') + 1);
        }
        return $data;
    }
}
