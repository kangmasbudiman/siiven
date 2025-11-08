<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shifts = Shift::all();
        return view('shift.index', compact('shifts'));
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
        
       // dd($request->all());
      

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
            ]);
          Shift::create($validatedData);

        return redirect()->route('shift.index')->with('success', 'Data shift berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
          return redirect()->back()
                ->with('error', '❌ Gagal menambahkan shift: ' . $e->getMessage())
                ->withInput();
        }

       


    }

   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
       
    $validatedData = $request->validate([
        'id' => 'required|integer|exists:shifts,id',
        'name' => 'nullable|string|max:255',
        'start_time' => 'nullable|date_format:H:i',
        'end_time' => 'nullable|date_format:H:i|after:start_time',
    ]);

    $shift = Shift::findOrFail($request->id);
    $shift->update($validatedData);
          return redirect()->route('shift.index')->with('success', 'Data shift berhasil diupate!');


      
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shift  $shift
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shift $shift)
    {

        try {
            $shift->delete();
            return redirect()->route('shift.index')->with('success', 'Data shift berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Gagal menghapus shift: ' . $e->getMessage());
        }
    }
}
