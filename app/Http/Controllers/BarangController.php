<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategoris;
use App\Services\KodeBarangGenerator;



class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $barangs=Barang::all();
        $kategori_barangs=Kategoris::all();

        return view('barang.index', compact('barangs','kategori_barangs'));
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
       try {
            $validatedData = $request->validate([
                
                'nama_barang' => 'required|string|max:255',
                'kategori_id' => 'required|integer',
                'jenis_barang' => 'required|string|max:255',
                'merk' => 'required|string|max:255',
                'spesifikasi' => 'required|string|max:255',
                'satuan' => 'required|string|max:255',
                
  
                 'is_active' => 'nullable|boolean',
            ]);
            $validatedData['kode_barang'] = KodeBarangGenerator::generate();

           // dd($validatedData);
            $barangs = Barang::create($validatedData);
            return redirect()->route('barang.index')->with('success', 'Data barang berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
                    'nama_barang' => 'required|string|max:255',
                    'kategori_id' => 'required|integer',
                    'jenis_barang' => 'required|string|max:255',
                    'merk' => 'required|string|max:255',
                    'spesifikasi' => 'required|string|max:255',
                    'satuan' => 'required|string|max:255',
                    'is_active' => 'nullable|boolean',
                ]);
    
                $barang = Barang::findOrFail($id);
                $barang->update($validatedData);
    
                return redirect()->route('barang.index')->with('success', 'Data barang berhasil diupdate!');
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
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus!');
    }
}
