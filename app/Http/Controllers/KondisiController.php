<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kondisis;



class KondisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kondisis=Kondisis::all();
        return view('kondisi.index', compact('kondisis'));
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
                'nama_kondisi' => 'required|string|max:255',
                'keterangan' => 'required|string|max:255',
                 
            ]);

            $kondisis = Kondisis::create($validatedData);
            return redirect()->route('kondisi.index')->with('success', 'Data kondisi berhasil disimpan!');
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
                'nama_kondisi' => 'required|string|max:255',
                'keterangan' => 'required|string|max:255',
                 
            ]);

            $kondisis = Kondisis::findOrFail($id);
            $kondisis->update($validatedData);

            return redirect()->route('kondisi.index')->with('success', 'Data kondisi berhasil diupdate!');
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
            $kondisis = Kondisis::findOrFail($id);
            $kondisis->delete();

            return redirect()->route('kondisi.index')->with('success', 'Data kondisi berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
