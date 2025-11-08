<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserShift;
use App\Models\User;
use App\Models\Shift;



class UserShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userShifts = UserShift::with(['user', 'shift', 'assignedBy'])->get();
        $users = User::where('hakAkses', '2')->get(); // Hanya admin
        $shifts = Shift::all();
        
        return view('usershift.index', compact('userShifts', 'users', 'shifts'));
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
    // Jalankan validasi
    $validated = $request->validate([
        'user_id' => 'required|exists:user,idUser', // Pastikan nama tabel sesuai
        'shift_id' => 'required|exists:shifts,id',
        'is_active' => 'required|boolean',
    ]);

    // Cek apakah user sudah memiliki shift aktif pada hari ini
    $existingShift = UserShift::where('user_id', $request->user_id)
        ->whereDate('created_at', date('Y-m-d'))
        ->where('is_active', true) // Hanya cek yang aktif
        ->first();

    if ($existingShift) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'User sudah memiliki shift aktif untuk hari ini. Nonaktifkan shift lama terlebih dahulu.');
    }

    // Buat user shift baru
    $userShift = UserShift::create([
        'user_id' => $request->user_id,
        'shift_id' => $request->shift_id,
        'is_active' => $request->is_active,
        'assigned_by' => auth()->user()->idUser,
    ]);

    return redirect()->route('usershift.index')
        ->with('success', 'User shift berhasil dibuat');

} catch (\Illuminate\Validation\ValidationException $e) {
    // Jika validasi gagal, redirect kembali dengan error dan input lama
    return redirect()->back()
        ->withErrors($e->errors())
        ->withInput();
        
} catch (\Exception $e) {
    // Tangani error lainnya
    return redirect()->back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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


    /**
     * Toggle the active status of a user shift.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {

       
        $userShift = UserShift::findOrFail($id);

        // Toggle the status
        $userShift->is_active = !$userShift->is_active;
        $userShift->save();

        return redirect()->route('usershift.index')
            ->with('success', 'Status user shift berhasil diubah');
    }
}
