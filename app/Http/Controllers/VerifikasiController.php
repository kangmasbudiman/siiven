<?php

namespace App\Http\Controllers;

use App\Models\ApprovalUsulanPembelian;
use Illuminate\Http\Request;

class VerifikasiController extends Controller
{
    public function verify($token)
    {
        $approval = ApprovalUsulanPembelian::with(['usulan.ruangan', 'approver'])
            ->where('token', $token)
            ->where('status', 'approved')
            ->first();

        return view('verify.index', compact('approval'));
    }
}
