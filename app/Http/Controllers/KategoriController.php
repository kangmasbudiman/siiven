<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategoris;



class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kategoris=Kategoris::all();
        return view('kategori.index', compact('kategoris'));
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
                'nama_kategori' => 'required|string|max:255',
                'keterangan' => 'required|string|max:255',
                 'is_active' => 'nullable|boolean',
            ]);

            $kategoris = Kategoris::create($validatedData);
            return redirect()->route('kategori.index')->with('success', 'Data kategori berhasil disimpan!');
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
                'nama_kategori' => 'nullable|string|max:255',
                'keterangan' => 'nullable|string|max:255',
                 'is_active' => 'nullable|boolean',
            ]);

            $kategoris = Kategoris::findOrFail($id);
            $kategoris->update($validatedData);
            return redirect()->route('kategori.index')->with('success', 'Data kategori berhasil diupdate!');
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
            $kategoris = Kategoris::findOrFail($id);
            $kategoris->delete();
            return redirect()->route('kategori.index')->with('success', 'Data kategori berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
