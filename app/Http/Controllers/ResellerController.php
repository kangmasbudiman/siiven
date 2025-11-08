<?php

namespace App\Http\Controllers;

use App\Models\Reseller;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $resellers = Reseller::orderBy('namereseller')->get();
        // dd($resellers);
        return view('reseller.index', compact('resellers'));
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
         $request->validate([
            'namereseller' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:resellers,code',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'initial_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'special_prices' => 'nullable|array',
            'special_prices.*' => 'nullable|numeric|min:0'
        ]);

        try {
            // Create reseller
            $reseller = Reseller::create([
                'namereseller' => $request->namereseller,
                'code' => strtoupper($request->code),
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'initial_balance' => $request->initial_balance ?? 0,
                'notes' => $request->notes,
                'is_active' => $request->has('is_active')
            ]);

      
            return redirect()->route('reseller.index')
                ->with('success', '🎉 Data reseller berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Gagal menambahkan reseller: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reseller  $reseller
     * @return \Illuminate\Http\Response
     */
    public function show(Reseller $reseller)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reseller  $reseller
     * @return \Illuminate\Http\Response
     */
    public function edit(Reseller $reseller)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reseller  $reseller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reseller $reseller)
    {
       $request->validate([
            'namereseller' => 'required|string|max:100',
            'code' => 'nullable|string|max:20|unique:resellers,code,' . $reseller->id,
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
            'initial_balance' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
            'special_prices' => 'nullable|array',
            'special_prices.*' => 'nullable|numeric|min:0'
        ]);

        try {
            // Update reseller
            $reseller->update([
                'namereseller' => $request->namereseller,
                'code' => strtoupper($request->code),
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'initial_balance' => $request->initial_balance ?? 0,
                'notes' => $request->notes,
                'is_active' => $request->has('is_active')
            ]);

      

            return redirect()->route('reseller.index')
                ->with('success', '✅ Data reseller berhasil diupdate!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Gagal mengupdate reseller: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reseller  $reseller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reseller $reseller)
    {
       // dd($reseller);
       try {
            if (!$reseller->canDelete()) {
                return redirect()->back()
                    ->with('warning', '⚠️ Tidak dapat menghapus reseller yang sudah memiliki riwayat transaksi!');
            }

           // $reseller->applications()->detach();
            $reseller->delete();

            return redirect()->route('reseller.index')
                ->with('success', '🗑️ Data reseller berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Gagal menghapus reseller: ' . $e->getMessage());
        }
    }




     public function toggleStatus(Reseller $reseller)
    {
        try {
            $reseller->update([
                'is_active' => !$reseller->is_active
            ]);

            $status = $reseller->is_active ? 'diaktifkan' : 'dinonaktifkan';
            $icon = $reseller->is_active ? '✅' : '❌';
            
            return redirect()->back()
                ->with('success', "$icon Reseller berhasil $status!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Gagal mengubah status reseller: ' . $e->getMessage());
        }
    }

    /**
     * Get reseller details for AJAX
     */
    public function getResellerDetails(Reseller $reseller)
    {
        $reseller->load('applications');
        return response()->json($reseller);
    }
}
