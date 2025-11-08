<?php

namespace App\Http\Controllers;

use App\Models\Aplikasi;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StockAppController extends Controller
{
    public function index()
    {
        $apps = Aplikasi::all();
        $logs = StockHistory::latest()->take(10)->get(); // Riwayat 10 perubahan stok terakhir
      
        return view('stockapp.index', compact('apps', 'logs'));
    }

  public function indexadmin(Request $request)
    {
        $startDate = $request->start_date;
        $endDate   = $request->end_date;

        $query = StockHistory::with(['aplikasi', 'user'])->orderBy('created_at', 'desc');

        // Jika user memilih periode tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                $startDate . " 00:00:00",
                $endDate . " 23:59:59"
            ]);
            $logs = $query->get(); // tampilkan semua dalam range
        } else {
            // Tanpa filter tanggal → tampilkan hanya 20 log terbaru
            $logs = $query->limit(20)->get();
        }

        $apps = Aplikasi::orderBy('nameaplication')->get();

        return view('stockapp.indexadmin', compact('apps', 'logs', 'startDate', 'endDate'));
    }


    public function reload(Request $request)
    {
        $request->validate([
            'app_id' => 'required',
            'reload_amount' => 'required|numeric|min:1',
            'note' => 'nullable',
        ]);

        $app = Aplikasi::findOrFail($request->app_id);
        $app->qty += $request->reload_amount;
        $app->save();

        StockHistory::create([
            'app_id' => $app->id,
            'amount' => $request->reload_amount,
            'type' => 'RELOAD',
            'note' => $request->note,
            'user_id' => auth()->id(),

        ]);

        return back()->with('success', 'Stok aplikasi berhasil ditambahkan ✅');
    }

public function exportPdf(Request $request)
{
    $startDate = $request->start_date;
    $endDate   = $request->end_date;

    $query = StockHistory::with(['aplikasi', 'user'])->orderBy('created_at', 'desc');

   // dd($request->all());

    if ($startDate && $endDate) {
        $query->whereBetween('created_at', [
            $startDate . " 00:00:00",
            $endDate . " 23:59:59"
        ]);
        $logs = $query->get();
    } else {
        $logs = $query->limit(50)->get(); // tampilkan 50 baris default saat export
    }


    $pdf = Pdf::loadView('stockapp.pdf', compact('logs', 'startDate', 'endDate'))
              ->setPaper('a4', 'portrait');

    return $pdf->stream('Laporan_Mutasi_Stok.pdf'); // stream = preview bukan download otomatis
}

}
