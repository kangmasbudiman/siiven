<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ShiftSession;
use App\Models\Aplikasi;
use App\Models\Reseller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ShiftMember;
use App\Models\Bank;
use App\Models\StockHistory;
class TransactionController extends Controller
{






public function index()
{

   


    $user_shift = session('user_shift');
    
    $iduser = $user_shift->user->idUser;

    // 1️⃣ Cari shift aktif milik user sendiri
    $activeShift = \App\Models\ShiftSession::where('status', 'ACTIVE')
        ->where('opened_by', $iduser)
        ->first();
        
       

        

   // 2) Cek apakah user sedang join shift orang lain
$joinedShift = ShiftMember::where('user_id', $iduser)
    ->whereHas('session', fn($q) => $q->where('status', 'ACTIVE'))
    ->with('session')
    ->first();

if ($joinedShift) {
    $activeShift = $joinedShift->session; // <- ini yang menentukan tampilan di dashboard
}

    // 4) Ambil shift terakhir yang pernah diikuti (untuk tombol Rejoin)
    $lastJoinedShift = ShiftMember::where('user_id', $iduser)
        ->latest()
        ->with('session')
        ->first();


    // 4️⃣ Ambil semua shift aktif (untuk daftar join)
    $activeShifts = \App\Models\ShiftSession::where('status', 'ACTIVE')
        ->with('openedBy')
        ->orderBy('start_time', 'desc')
        ->get();

    // 🧩 Cek transaksi hanya kalau user aktif di shift
    $pendingTransactions = $completedTransactions = $newTransactions = collect();
    if ($activeShift) {
        $pendingTransactions = Transaction::with('application')
            ->where('shift_session_id', $activeShift->id)
            ->where('status', 'PENDING')
            ->get();

        $completedTransactions = Transaction::with('application')
            ->where('shift_session_id', $activeShift->id)
            ->where('status', 'DONE')
            ->whereDate('created_at', today())
            ->get();

        $newTransactions = Transaction::with('application')
            ->where('shift_session_id', $activeShift->id)
            ->where('status', 'PENDING')
            ->where('admin_id', $iduser)
            ->get();
    }



        

// jika user TIDAK punya shift aktif sendiri, tapi pernah join shift yang masih ACTIVE → pakai itu
//if (!$activeShift && $lastJoinedShift && $lastJoinedShift->session && $lastJoinedShift->session->status === 'ACTIVE') {
//    $activeShift = $lastJoinedShift->session;
//}


    $applications = Aplikasi::active()->get();
    $bankAccounts = \App\Models\Bank::all();
    $resellers = Reseller::active()->get();

$lastJoinedShift = ShiftMember::where('user_id', $iduser)
    ->whereHas('session', fn($q) => $q->where('status', 'ACTIVE'))
    ->orderBy('id', 'desc')
    ->with('session.openedBy')
    ->first();





    return view('homeAdmin', compact(
        'user_shift',
        'activeShift',
        'joinedShift',
        'lastJoinedShift', 
        
        'activeShifts',
        'pendingTransactions',
        'completedTransactions',
        'newTransactions',
        'applications',
        'bankAccounts',
        'resellers',
    ));
}




    public function indexcccc()
{
    $user_shift = session('user_shift');
    $iduser = $user_shift->user->idUser;

    // 🔹 Cari shift aktif yang dibuka oleh user (jika ada)
    $activeShift = \App\Models\ShiftSession::where('status', 'ACTIVE')
        ->where('opened_by', $iduser)
        ->first();

    // 🔹 Jika tidak ada, cari shift yang user ikuti (join)
    if (!$activeShift) {
       $joinedShift = \App\Models\ShiftMember::where('user_id', $iduser)
    ->whereNull('left_at') // ✅ pastikan belum keluar
    ->whereHas('session', fn($q) => $q->where('status', 'ACTIVE'))
    ->with('session')
    ->first();

        if ($joinedShift) {
            $activeShift = $joinedShift->session; // Gunakan shift yang diikuti
        }
    }

    // 🔹 Jika masih tidak ada, berarti user belum join / belum mulai shift
    if (!$activeShift) {
        $pendingTransactions = collect();
        $completedTransactions = collect();
        $newTransactions = collect();
    } else {
        // 🔹 Transaksi Pending
        $pendingTransactions = \App\Models\Transaction::with('application')
            ->where('shift_session_id', $activeShift->id)
            ->where('status', 'PENDING')
            ->orderByDesc('id')
            ->get();

        // 🔹 Transaksi Selesai
        $completedTransactions = \App\Models\Transaction::with('application')
            ->where('shift_session_id', $activeShift->id)
            ->where('status', 'DONE')
            ->whereDate('created_at', today())
            ->orderByDesc('id')
            ->get();

        // 🔹 Transaksi Baru milik user
        $newTransactions = \App\Models\Transaction::where('shift_session_id', $activeShift->id)
            ->where('status', 'PENDING')
            ->where('admin_id', $iduser)
            ->latest()
            ->get();
    }

    // 🔹 Data tambahan untuk modal & dropdown
    $applications = \App\Models\Aplikasi::active()->get();
    $bankAccounts = \App\Models\Bank::all();
    $resellers = \App\Models\Reseller::active()->get();

    // 🔹 Semua shift aktif (untuk join)
    $activeShifts = \App\Models\ShiftSession::where('status', 'ACTIVE')
        ->orderBy('start_time', 'desc')
        ->with('openedBy')
        ->get();

    // 🔹 Shift yang sedang diikuti user (jika ada)
    $joinedShift = \App\Models\ShiftMember::where('user_id', $iduser)
        ->whereHas('session', fn($q) => $q->where('status', 'ACTIVE'))
        ->with('session')
        ->first();

    return view('homeAdmin', compact(
        'activeShift',
        'activeShifts',
        'joinedShift',
        'user_shift',
        'applications',
        'pendingTransactions',
        'completedTransactions',
        'newTransactions',
        'bankAccounts',
        'resellers',
    ));
}








    public function create()
    {
        $activeShift = ShiftSession::where('admin_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            return redirect()->route('shift.create')
                ->with('error', 'Silakan mulai shift terlebih dahulu!');
        }

        $applications = Application::active()->get();
        $resellers = Reseller::active()->get();

     
    }

    public function store(Request $request)
    {
     
      
        $validated = $request->validate([
                'app_id' => 'required|exists:aplikasis,id',
                'amount_due' => 'required|numeric|min:1',
                'payment_type' => 'required|in:Lunas,Kasbon',
                'payment_method' => 'required',
                'amount_paid' => 'nullable|numeric|min:0',
                'customer_type' => 'required|in:Reseller,Non-Reseller',
                'reseller_id' => 'nullable|exists:resellers,id',
                'customer_phone' => 'nullable|string|max:30',
                'notes_transaction' => 'nullable|string|max:500',
                'payment_account_id' => 'nullable|exists:banks,id',
         ]);

         $app = Aplikasi::find($request->app_id);

        // ✅ Hitung coin qty
        $coinQty = $validated['amount_due'] / $app->rate;

        // ✅ Cek saldo stok sebelum diproses
        if ($app->qty < $coinQty) {
            return back()->with('error', 'Stok aplikasi tidak cukup untuk transaksi ini!')->withInput();
        }



         if ($app) {
            
            $app->qty -= $request->coin_qty;
            if ($app->qty < 0) $app->qty = 0; // biar gak minus
            $app->save();

             StockHistory::create([
            'app_id' => $app->id,
            'amount' => $request->coin_qty,
            'type' => 'TRANSACTION',
            'note' => 'transaksi penjualan',
            'user_id' => auth()->id(),

        ]);
                        $user_shift = session('user_shift');
                        $iduser=$user_shift->user->idUser;
                        $activeShift = ShiftSession::where('status', 'ACTIVE')
                        ->where('opened_by', $iduser)
                        ->firstOrFail();

                        // ✅ Jika reseller, hitung diskon
if ($validated['customer_type'] === "Reseller" && $validated['reseller_id']) {
    $reseller = Reseller::find($validated['reseller_id']);
    if ($reseller && $reseller->diskon > 0) {
        $diskonPersen = $reseller->diskon; // contoh: 10 berarti 10%
        $diskonNominal = ($validated['amount_due'] * $diskonPersen) / 100;
        $validated['amount_due'] -= $diskonNominal; // Harga setelah diskon
    }
}



                        $application = Aplikasi::findOrFail($validated['app_id']);
                  Transaction::create([
                            ...$validated,
                            'shift_session_id' => $activeShift->id,
                            'admin_id' => auth()->id(),
                            'coin_type' => $application->coin_type,
                            'rate' => $application->rate,
                            'diskon' => $diskonNominal,
                            'status' => 'PENDING',
                        ]);

                        return back()->with('success', 'Transaksi berhasil disimpan!');
        }else{
            return back()->with('error', 'Stok aplikasi tidak cukup!');
        }


                
               

    }

    public function process(Request $request)
{
    $request->validate([
        'id' => 'required|exists:transactions,id',
        'status' => 'required|in:DONE,FAILED,CANCELLED',
        'process_notes' => 'nullable|string|max:500',
    ]);

    $trx = Transaction::findOrFail($request->id);
    $trx->update([
        'status' => $request->status,
        'processed_by' => auth()->id(),
        'processed_datetime' => now(),
        'process_notes' => $request->process_notes,
    ]);

    return back()->with('success', 'Transaksi berhasil diproses.');
}





    public function storeReload(Request $request)
    {
        $request->validate([
            'application_id' => 'required|exists:applications,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $activeShift = ShiftSession::where('admin_id', Auth::id())
            ->where('status', 'open')
            ->firstOrFail();

        Transaction::create([
            'shift_session_id' => $activeShift->id,
            'application_id' => $request->application_id,
            'type' => 'reload',
            'quantity' => $request->quantity,
            'amount' => 0,
            'notes' => $request->notes
        ]);

        return response()->json(['success' => true, 'message' => 'Top-up berhasil dicatat!']);
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'reseller_id' => 'required|exists:resellers,id',
            'amount' => 'required|numeric|min:1'
        ]);

        $activeShift = ShiftSession::where('admin_id', Auth::id())
            ->where('status', 'open')
            ->firstOrFail();

        Transaction::create([
            'shift_session_id' => $activeShift->id,
            'reseller_id' => $request->reseller_id,
            'type' => 'payment',
            'quantity' => 0,
            'amount' => $request->amount,
            'notes' => $request->notes
        ]);

        return response()->json(['success' => true, 'message' => 'Pembayaran hutang berhasil dicatat!']);
    }



    public function update (Request $request)
    {


     //   dd($request->all());

       try {
        $validated = $request->validate([
            'id' => 'required|exists:transactions,id',
            'app_id' => 'required|exists:aplikasis,id',
            'amount_due' => 'required|numeric|min:1',
            'payment_type' => 'required|in:Lunas,Kasbon',
            'payment_method' => 'required',
            'amount_paid' => 'nullable|numeric|min:0',
            'customer_type' => 'required|in:Reseller,Non-Reseller',
            'reseller_id' => 'nullable|exists:resellers,id',
            'customer_phone' => 'nullable|string|max:30',
            'notes_transaction' => 'nullable|string|max:500',
            'payment_account_id' => 'nullable|exists:banks,id',
        ]);

         $trx = Transaction::findOrFail($request->id);

        // Ambil data sebelum update
        $old_qty = $trx->coin_qty;
        $new_qty = $request->coin_qty;
        $app_id  = $request->app_id;

       // dd($old_qty, $new_qty, $app_id);



        $transaction = Transaction::findOrFail($validated['id']);
        $user_shift = session('user_shift');
        $iduser=$user_shift->user->idUser;
        $activeShift = ShiftSession::where('status', 'ACTIVE')
        ->where('opened_by', $iduser)
        ->firstOrFail();


            $application = Aplikasi::findOrFail($validated['app_id']);

                        // ✅ Jika reseller, hitung diskon
            if ($validated['customer_type'] === "Reseller" && $validated['reseller_id']) {
                $reseller = Reseller::find($validated['reseller_id']);
                if ($reseller && $reseller->diskon > 0) {
                    $diskonPersen = $reseller->diskon; // contoh: 10 berarti 10%
                    $diskonNominal = ($validated['amount_due'] * $diskonPersen) / 100;
                    $validated['amount_due'] -= $diskonNominal; // Harga setelah diskon
                }
            }



            $transaction->update([
                ...$validated,
                'shift_session_id' => $activeShift->id,
                'admin_id' => auth()->id(),
                'coin_type' => $application->coin_type,
                'rate' => $application->rate,
                'diskon' => $diskonNominal,
                'status' => 'PENDING',
            ]);
            // === Sesuaikan STOK === //
            $app = Aplikasi::find($app_id);

               // ✅ Hitung coin qty
        $coinQty = $validated['amount_due'] / $app->rate;

        // ✅ Cek saldo stok sebelum diproses
        if ($app->qty < $coinQty) {
            return back()->with('error', 'Stok aplikasi tidak cukup untuk transaksi ini!')->withInput();
        }



            if ($app) {
                // Kembalikan stok lama, potong stok baru
                $app->qty = $app->qty + $old_qty - $new_qty;

                // Mencegah stok minus
                if ($app->qty < 0) {
                    $app->qty = 0;
                }

                $app->save();
            }

            
            StockHistory::create([
                    'app_id' => $app->id,
                    'amount' => $request->coin_qty,
                    'type' => 'EDIT TRANSACTION',
                    'note' => 'Edit Transaksi dari ' . $old_qty . ' menjadi ' . $new_qty,
                    'user_id' => auth()->id(),
            ]);

            return back()->with('success', 'Transaksi berhasil disimpan!');
            } catch (\Exception $e) {
            
                return back()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
            }
    }

    public function destroy( $id)
    {
       try {

                $transaction = Transaction::findOrFail($id);
                $transaction->delete();
                return back()->with('success', 'Transaksi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }



    public function approve(Request $request, $id)
    {
  


       try {
                $transaction = Transaction::findOrFail($id);
                $transaction->update([
                    'status' => 'DONE',
                    'processed_by' => auth()->id(),
                    'processed_datetime' => now(),
                    'process_notes' => $request->payment_notes,
                ]);

                





                return back()->with('success', 'Transaksi berhasil diapprove!');

        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->with('error', 'Gagal approve transaksi: ' . $e->getMessage());
        }
    }



    public function laporanperiode(Request $request){

    $from = $request->from;
        $to = $request->to;

        $query = ShiftSession::query();

        if ($from && $to) {
            $query->whereBetween('start_time', [$from . " 00:00:00", $to . " 23:59:59"]);
        }

        $shifts = $query->orderBy('start_time', 'desc')->get();

        return view('transaction.laporan-periode', compact('shifts', 'from', 'to'));
            
    }


    public function laporanPeriodePdf(Request $request)
    {

    
        $from = $request->from;
        $to = $request->to;

        $shifts = ShiftSession::whereBetween('start_time', [$from." 00:00:00", $to." 23:59:59"])
            ->orderBy('start_time', 'desc')
            ->get();

        $pdf = \PDF::loadView('transaction.laporanperiodePdf', compact('shifts', 'from', 'to'))->setPaper('A4', 'portrait');

        return $pdf->stream('Laporan-Shift-'.$from.'-sampai-'.$to.'.pdf');

    }



    public function reload(Request $request){

    //  $app_id = $request->app_id;
        $application = Aplikasi::findOrFail($app_id);

        return view('transaction.reload', compact('application'));

    }







}