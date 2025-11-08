<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = Bank::orderBy('bank_name')->get();
        return view('bank.index', compact('banks'));
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
            $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:20|unique:banks,account_number',
            'account_name' => 'required|string|max:100',
            'branch' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255'
        ]);

        try {
            Bank::create([
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'branch' => $request->branch,
                'notes' => $request->notes,
                'is_active' => $request->has('is_active')
            ]);

            return redirect()->route('bank.index')
                ->with('success', '🎉 Data bank berhasil ditambahkan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Gagal menambahkan bank: ' . $e->getMessage())
                ->withInput();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {

      
       

      $validatedData = $request->validate([
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:20|unique:banks,account_number,' . $bank->id,
            'account_name' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:255'
        ]);

       $bank = Bank::findOrFail($request->id);
    $bank->update($validatedData);

        return redirect()->route('bank.index')->with('success', 'Data bank berhasil diupate!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
      try {
            if (!$bank->canDelete()) {
                return redirect()->back()
                    ->with('warning', '⚠️ Tidak dapat menghapus bank yang sudah memiliki riwayat transaksi!');
            }

            $bank->delete();

            return redirect()->route('bank.index')
                ->with('success', '🗑️ Data bank berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Gagal menghapus bank: ' . $e->getMessage());
        }
    }

     public function toggleStatus(Bank $bank)
    {
        try {
            $bank->update([
                'is_active' => !$bank->is_active
            ]);

            $status = $bank->is_active ? 'diaktifkan' : 'dinonaktifkan';
            $icon = $bank->is_active ? '✅' : '❌';
            
            return redirect()->back()
                ->with('success', "$icon Bank berhasil $status!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Gagal mengubah status bank: ' . $e->getMessage());
        }
    }



}
