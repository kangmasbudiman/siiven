<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Dashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Approver (Kabag, Direktur, Ka.Keuangan, Bendahara) langsung ke dashboard mereka
        if ($user->approval_level) {
            return $next($request);
        }

        if ($user->can('isGudang')) {
            return redirect()->route('stuff.index');
        } else if ($user->can('isKasir')) {
            return redirect()->route('transaction.index');
        }

        return $next($request);
    }
}
