<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockBarang;
use App\Models\Kondisis;
use App\Models\Barang;
use App\Models\Ruangan;




class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     

        $inventories = StockBarang::where('ruangan_id', auth()->user()->ruangan_id)->with('kondisi')->get();
        $kondisis = Kondisis::all();
        $barangs = Barang::all();
        return view('inventory.index', compact('inventories', 'kondisis', 'barangs'));
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

        $searchBarang = StockBarang::where([['barang_id', $request->barang_id], ['ruangan_id', auth()->user()->ruangan_id]])->first();
        if($searchBarang == null){
        // Ambil user_id dari user yang sedang login
        $ruangan_id = auth()->user()->ruangan_id;
        $namaRuangan = Ruangan::where('id', $ruangan_id)->first()->nama_ruangan;
              try {
            $validatedData = $request->validate([
                'barang_id' => 'required|integer',
                'harga' => 'required|integer',
                'kondisi_id' => 'required|integer',
                'jumlah' => 'required|integer',
                'keterangan' => 'nullable|string|max:255',
                'tanggalPembelian' => 'nullable|date',
                'merk' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:255',
                'nomorSeri' => 'nullable|string|max:255',
                'kondisiPembelian' => 'nullable|string|max:255',
                'tanggalPenerimaan' => 'nullable|date',
            ]);
            $validatedData['nomorInventaris'] = 'RPJ/'.$request->barang_id . '/' . $namaRuangan . '/' . date('YmdHis');
            $validatedData['ruangan_id'] = $ruangan_id;
            $validatedData['user_id'] = auth()->user()->idUser;
          
            StockBarang::create($validatedData);

            return redirect()->route('inventory.index')->with('success', 'Data inventory berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        }else{
            return redirect()->back()->with('error', 'Barang sudah ada di ruangan ini silahkan update jumlahnya!');
        }
      

      



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
        try {
            $validatedData = $request->validate([
                'barang_id' => 'required|integer',
                'kondisi_id' => 'required|integer',
                'jumlah' => 'required|integer',
                'harga' => 'required|integer',
                'keterangan' => 'nullable|string|max:255',
                'tanggalPembelian' => 'nullable|date',
                'merk' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:255',
                'nomorSeri' => 'nullable|string|max:255',
                'kondisiPembelian' => 'nullable|string|max:255',
                'tanggalPenerimaan' => 'nullable|date',
            ]);

             $inventory = StockBarang::findOrFail($id);
            $inventory->update($validatedData);

            return redirect()->route('inventory.index')->with('success', 'Data inventory berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $inventory = StockBarang::findOrFail($id);
            $inventory->delete();

            return redirect()->route('inventory.index')->with('success', 'Data inventory berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
