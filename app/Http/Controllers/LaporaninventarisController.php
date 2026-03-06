<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockBarang;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Kondisis;

class LaporaninventarisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ruangans = Ruangan::all();
        $inventaris = StockBarang::all();
        $barangs = Barang::all();
        $kondisis = Kondisis::all();
        return view('laporan.index', compact('inventaris', 'ruangans', 'barangs', 'kondisis'));
    }


      private function stokQuery(Request $request)
    {
        $query = StockBarang::with(['barang', 'ruangan', 'kondisi']);

        // 🔐 Batasi user ruangan
        if (auth()->user()->hakAkses != 1) {
            $query->where('ruangan_id', auth()->user()->ruangan_id);
        }

        // 🔎 FILTER
        if ($request->ruangan_id) {
            $query->where('ruangan_id', $request->ruangan_id);
        }

        if ($request->barang_id) {
            $query->where('barang_id', $request->barang_id);
        }

        if ($request->kondisi_id) {
            $query->where('kondisi_id', $request->kondisi_id);
        }

        return $query;
    }

     public function stok(Request $request)
    {
        $data = $this->stokQuery($request)->get();

        return view('laporan.stock', [
            'data'     => $data,
            'ruangans' => Ruangan::all(),
            'barangs'  => Barang::all(),
            'kondisis' => Kondisi::all(),
        ]);
    }
      public function pdf(Request $request)
{
    $data = $this->stokQuery($request)->get();

    // ✅ GROUP DATA BERDASARKAN NAMA RUANGAN
    $groupedData = $data->groupBy(function ($item) {
        return $item->ruangan->nama_ruangan;
    });

    // ✅ KIRIM groupedData KE VIEW
    $pdf = \PDF::loadView('laporan.stock', [
        'groupedData' => $groupedData
    ])->setPaper('A4', 'landscape');

    // ✅ STREAM TANPA DATA TAMBAHAN
    return $pdf->stream('laporan-stok.pdf');
}


        public function exel(Request $request)
    {
        return \Excel::download(
            new \App\Exports\StokBarangExport($request),
            'laporan-stok.xlsx'
        );
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
