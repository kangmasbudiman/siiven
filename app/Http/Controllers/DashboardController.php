<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $activeShift = \App\Models\ShiftSession::with('shift')
            ->where('admin_id', Auth::id())
            ->where('status', 'open')
            ->first();

        return view('dashboard', compact('activeShift'));
    }
}