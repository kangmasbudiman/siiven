<?php

namespace App\Http\Controllers;

use App\Models\Aplikasi;
use Illuminate\Http\Request;

class AplikasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $aplikasis=Aplikasi::all();
         
            return view('aplikasi.index', compact('aplikasis'));
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
                'nameaplication' => 'required|string|max:255',
                'code' => 'required|string',
                'normal_price' => 'required|string',
                'coin_type' => 'required|string',
                'rate' => 'required|string',
                'description' => 'required|string',
                'is_active' => 'required|string',
               
            ]);
        Aplikasi::create($validatedData);
        return redirect()->route('aplikasi.index')->with('success', 'Data aplikasi berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
          return redirect()->back()
                ->with('error', '❌ Gagal menambahkan shift: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Aplikasi  $aplikasi
     * @return \Illuminate\Http\Response
     */
    public function show(Aplikasi $aplikasi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Aplikasi  $aplikasi
     * @return \Illuminate\Http\Response
     */
    public function edit(Aplikasi $aplikasi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Aplikasi  $aplikasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

       
          $validatedData = $request->validate([
        'id' => 'required|integer|exists:aplikasis,id',
        'nameaplication' => 'nullable|string|max:255',
        'code' => 'nullable|string',
        'normal_price' => 'nullable|string',
        'coin_type' => 'nullable|string',
        'rate' => 'nullable|string',
        'description' => 'nullable|string',
        'is_active' => 'nullable|string',
    ]);

    $aplikasi = Aplikasi::findOrFail($request->id);
    $aplikasi->update($validatedData);
          return redirect()->route('aplikasi.index')->with('success', 'Data aplikasi berhasil diupate!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Aplikasi  $aplikasi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Aplikasi $aplikasi)
    {

         try {
            $aplikasi->delete();
            return redirect()->route('aplikasi.index')->with('success', 'Data aplikasi berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal menghapus aplikasi: ' . $e->getMessage());
        }
    }
}
