<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\User;

class RuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ruangans = Ruangan::with(['user', 'approver'])->get();
        $approvers = User::whereNotNull('approval_level')->where('approval_level', 1)
            ->orderBy('nama')->get(['idUser', 'nama', 'jabatan']);

        return view('ruangan.index', compact('ruangans', 'approvers'));
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


     
    //    dd($request->all());

       
            try {
                $validatedData = $request->validate([
                    'kode_ruangan' => 'required|string|max:255',
                    'nama_ruangan' => 'required|string|max:255',
                    'jenis_ruangan' => 'required|string|max:255',
                    'gedung' => 'required|string|max:255',
                    'lantai' => 'required|string|max:255',
                    'penanggung_jawab' => 'required|string|max:255',
                    'jabatan' => 'required|string|max:255',
                    'kontak' => 'required|string|max:255',
                    'approver_id' => 'nullable|string',
                    'is_active' => 'nullable|boolean',
                ]);
                  $validatedData['user_id'] = auth()->id();
                  Ruangan::create($validatedData);
   

              return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil ditambahkan!');
              } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->back()
                      ->with('error', '❌ Gagal menambahkan ruangan: ' . $e->getMessage())
                      ->withInput();
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
              $validatedData = $request->validate([
                    'kode_ruangan' => 'nullable|string|max:255',
                    'nama_ruangan' => 'nullable|string|max:255',
                    'jenis_ruangan' => 'nullable|string|max:255',
                    'gedung' => 'nullable|string|max:255',
                    'lantai' => 'nullable|string|max:255',
                    'penanggung_jawab' => 'nullable|string|max:255',
                    'jabatan' => 'nullable|string|max:255',
                    'kontak' => 'nullable|string|max:255',
                    'approver_id' => 'nullable|string',
                    'is_active' => 'nullable|boolean',
                ]);

                $ruangan = Ruangan::findOrFail($id);
                $validatedData['user_id'] = auth()->id();

                $ruangan->update($validatedData);
                return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil diupdate!');




    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ruangan $ruangan)
    {
        try {
            $ruangan->delete();
            return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('ruangan.index')->with('error', '❌ Gagal menghapus ruangan: ' . $e->getMessage());
        }
    }
}
