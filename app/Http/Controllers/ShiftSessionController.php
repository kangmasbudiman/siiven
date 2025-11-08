<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use App\Models\ShiftSession;
use App\Models\ShiftMember;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;



class ShiftSessionController extends Controller
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

    public function start(Request $request)
    {
        
           $user_shift = session('user_shift');
           $shiftId= $user_shift->shift->id;


           $request->validate([
           // 'shift_id' => 'required|exists:shifts,id',
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

      //  session(['active_shift' => $shiftSession->id]);
        // Pastikan tidak ada shift yang masih aktif
        $active = ShiftSession::where('status', 'open')->where('opened_by', Auth::id())->first();
        if ($active) {
            return back()->with('error', 'Shift masih berjalan, tutup shift sebelumnya terlebih dahulu.');
        }

        ShiftSession::create([
            'shift_id' => $shiftId,
            'session_code' => 'S-' . strtoupper(uniqid()),
            'start_time' => now(),
            'opened_by' => Auth::id(),
            'status' => 'ACTIVE',
            'opening_balance' => $request->opening_balance,
            'note' => $request->notes,
        ]);

        return back()->with('success', 'Shift dimulai dengan saldo awal: ' . number_format($request->opening_balance, 0, ',', '.'));
  
    }


    public function end(Request $request){

        
        //dd($request->all());
        $activeShift = ShiftSession::where('id', $request->id)
        ->where('status', 'ACTIVE')
        ->first();

        if (!$activeShift) {
            return back()->with('error', 'Tidak ada shift yang sedang berjalan.');
        }

        $activeShift->update([
            'end_time' => now(),
            'status' => 'CLOSED',
            'close_balance' => $request->closing_balance,
            'closing_note' => $request->closing_notes,
            'closed_by' => Auth::id(),
            'difference_responsible_id' => $request->difference_responsible_id,
            'difference_note' => $request->difference_note,
        ]);

        return back()->with('success', 'Shift selesai!');
    }


    public function approve(){
        $activeShift = ShiftSession::where('status', 'ACTIVE')->first();
        if (!$activeShift) {
            return back()->with('error', 'Tidak ada shift yang sedang berjalan.');
        }

        $activeShift->update([
            'end_time' => now(),
            'status' => 'CLOSED',
        ]);

        return back()->with('success', 'Shift selesai!');
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

    public function joinShift(Request $request)
{




    $activeShift = ShiftSession::where('id', $request->shift_id)->latest()->first();

 


    if (!$activeShift) {
        return back()->with('error', 'Tidak ada shift aktif untuk join.');
    }

    $alreadyJoined = ShiftMember::where('shift_session_id', $activeShift->id)
        ->where('user_id', auth()->id())
        ->exists();

    if ($alreadyJoined) {
        return back()->with('info', 'Kamu sudah bergabung dalam shift ini.');
    }

    ShiftMember::create([
        'shift_session_id' => $activeShift->id,
        'user_id' => auth()->id(),
        'joined_at' => now(),
    ]);

    return back()->with('success', 'Berhasil join shift aktif.');

}

public function leaveShift(Request $request)
{
  $userId = auth()->id();

    // Hapus keanggotaan dari shift aktif
    ShiftMember::where('user_id', $userId)
        ->whereHas('session', function($q){
            $q->where('status', 'ACTIVE');
        })
        ->delete();

    // Hapus shift aktif dari session
    session()->forget('active_shift');

    return redirect()->back()->with('success', 'Kamu telah keluar dari shift.');
}



public function rejoin(Request $request)
{
    $userId = auth()->id();
    $shiftId = $request->shift_id;

    // Cek apakah shift masih aktif
    $shift = \App\Models\ShiftSession::where('id', $shiftId)
        ->where('status', 'ACTIVE')
        ->first();

    if (!$shift) {
        return back()->with('error', 'Shift ini sudah ditutup atau tidak tersedia untuk rejoin.');
    }

    // Cek apakah user sebelumnya pernah join shift ini
    $shiftMember = \App\Models\ShiftMember::where('shift_session_id', $shiftId)
        ->where('user_id', $userId)
        ->first();

    if (!$shiftMember) {
        return back()->with('error', 'Kamu belum pernah bergabung di shift ini sebelumnya.');
    }

    // Update data agar dianggap aktif lagi di shift ini
    $shiftMember->update([
        'joined_at' => now(),
        'left_at' => null,
    ]);

    return back()->with('success', 'Berhasil bergabung kembali ke shift sebelumnya.');
}




public function history(){
    $userId = auth()->id();

    $history = \App\Models\ShiftSession::where('status', 'CLOSED')
        ->where(function($q) use ($userId) {
            $q->where('opened_by', $userId) // User pembuka shift
              ->orWhereHas('members', function($q2) use ($userId) {
                  $q2->where('user_id', $userId); // User yang join shift
              });
        })
        ->with(['openedBy'])
        ->orderBy('end_time', 'desc')
        ->get();

       


    return view('shiftsessionhistory.index', compact('history'));
}

public function view($id){
    $shiftSession = \App\Models\ShiftSession::with(['openedBy', 'members.user'])
        ->findOrFail($id);
        
    return view('shiftsessionhistory.view', compact('shiftSession'));


}

public function detailshift(Request $request,$id){

    $shift = ShiftSession::with(['openedBy', 'members.user'])
        ->findOrFail($id);

    $transactions = Transaction::with('application')
        ->where('shift_session_id', $id)
        ->orderBy('id', 'desc')
        ->get();

    // Summary Calculation
    $totalAmount = $transactions->sum('amount_due');
    $totalPaid = $transactions->sum('amount_paid');
    $kasbon = $transactions->sum('outstanding_amount');

    //untuk menghitung selisih kas 
    $totalPaid   = $transactions->sum('amount_paid');
$closeBalance = $shift->closing_balance ?? 0;

$saldoSeharusnya = $shift->opening_balance + $totalPaid;
$selisihKas = $saldoSeharusnya - $closeBalance;
    return view('shiftsessionhistory.detail', compact(
        'shift', 
        'transactions',
        'totalAmount',
        'totalPaid',
        'kasbon',
        'selisihKas'
    ));

}

public function  listshiftsession(){
    $shiftsessions = ShiftSession::with(['openedBy', 'members.user'])
        ->orderBy('id', 'desc')
        ->get();
       // dd($shiftsessions);

    return view('shiftsessionhistory.list', compact('shiftsessions'));
}


public function report($id){
  
    $shift = \App\Models\ShiftSession::with('openedBy')->findOrFail($id);

    // Ambil semua transaksi dalam shift ini
    $transactions = \App\Models\Transaction::with('application')
        ->where('shift_session_id', $id)
        ->orderBy('id', 'desc')
        ->get();

    // Hitung total kas
    $totalAmountDue = $transactions->sum('amount_due');      // Total Tagihan
    $totalPaid = $transactions->sum('amount_paid');          // Yang sudah dibayar
    $totalOutstanding = $transactions->sum('outstanding_amount'); // Kasbon / Hutang

    // Hitung selisih kas setelah tutup shift
    $selisihKas = null;
    if ($shift->closing_balance !== null) {
        $selisihKas = $shift->closing_balance - $shift->opening_balance - $totalPaid;
    }

    return view('shiftsessionhistory.report', compact(
        'shift',
        'transactions',
        'totalAmountDue',
        'totalPaid',
        'totalOutstanding',
        'selisihKas'
    ));
}


public function getpdf($id)
{
    // Ambil data shift
    $shift = \App\Models\ShiftSession::with('openedBy')->findOrFail($id);

    // Ambil semua transaksi dalam shift ini
    $transactions = \App\Models\Transaction::with('application')
        ->where('shift_session_id', $id)
        ->orderBy('id', 'desc')
        ->get();

    // Hitung total kas
    $totalAmountDue = $transactions->sum('amount_due');      // Total Tagihan
    $totalPaid = $transactions->sum('amount_paid');          // Yang sudah dibayar
    $totalOutstanding = $transactions->sum('outstanding_amount'); // Kasbon / Hutang

    // Hitung selisih kas setelah tutup shift
    $selisihKas = null;
    if ($shift->closing_balance !== null) {
        $selisihKas = $shift->closing_balance - $shift->opening_balance - $totalPaid;
    }

    // Load view ke PDF
    $pdf = Pdf::loadView('shiftsessionhistory.pdf', compact(
        'shift',
        'transactions',
        'totalAmountDue',
        'totalPaid',
        'totalOutstanding',
        'selisihKas'
    ));

    // Download PDF dengan nama file yang sesuai
    return $pdf->stream('Laporan_Shift_' . $shift->session_code . '.pdf');
}





}
