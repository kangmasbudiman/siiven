<?php

namespace App\Http\Controllers;

use App\Models\PerawatanBarang;
use App\Models\StockBarang;
use Illuminate\Http\Request;

class PublicPerawatanController extends Controller
{
    public function scan(Request $request)
    {
        $code = trim($request->query('code', ''));

        if ($code !== '') {
            $stock = StockBarang::with(['barang', 'ruangan', 'kondisi'])
                ->where('nomorInventaris', $code)
                ->first();

            if ($stock) {
                return redirect()->route('public.perawatan.show', $stock->id);
            }

            return redirect()->route('public.perawatan.scan')
                ->with('error', "Nomor inventaris \"{$code}\" tidak ditemukan.");
        }

        return view('perawatan.public_scan');
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
            return redirect()->route('public.perawatan.scan')
                ->with('error', "Nomor inventaris \"{$code}\" tidak ditemukan.")
                ->withInput();
        }

        return redirect()->route('public.perawatan.show', $stock->id);
    }

    public function show($stockId)
    {
        $stock = StockBarang::with(['barang', 'ruangan', 'kondisi'])
            ->whereNotNull('nomorInventaris')
            ->where('nomorInventaris', '!=', '')
            ->findOrFail($stockId);

        $riwayat = PerawatanBarang::with('teknisi')
            ->byStock($stockId)
            ->latestFirst()
            ->paginate(15);

        $totalPerawatan = PerawatanBarang::byStock($stockId)->count();
        $terakhir = PerawatanBarang::byStock($stockId)->latestFirst()->first();

        return view('perawatan.public_show', compact('stock', 'riwayat', 'totalPerawatan', 'terakhir'));
    }
}
