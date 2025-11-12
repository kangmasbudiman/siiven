<?php

namespace App\Http\Controllers\Auth;

use App\Services\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Controllers\Controller;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{

	protected $auth;

	public function __construct(AuthService $auth)
	{
		$this->middleware('guest')->except('logout');
		$this->auth = $auth;
	}

	public function showLoginForm(): View
	{
		return view('auth.login');
	}

	public function login(LoginRequest $request): RedirectResponse
	{
		$credentials = $request->only('username', 'password');
		$remember = $request->filled('remember');

		if ($this->auth->login($credentials, $remember)) {
	
			$user = auth()->user();

		// Skip shift check for superadmin
if ($user->hakAkses !== '1') {
    $currentTime = now()->format('H:i:s');
    
    $shift = \App\Models\UserShift::join('shifts', 'shifts.id', '=', 'user_shifts.shift_id')
        ->where('user_shifts.is_active', true)
        ->where('user_shifts.user_id', $user->idUser)
       // ->where('shifts.is_active', true)
        ->whereTime('shifts.start_time', '<=', $currentTime)
        ->whereTime('shifts.end_time', '>=', $currentTime)
        ->select('user_shifts.*', 'shifts.name as shift_name', 'shifts.start_time', 'shifts.end_time')
        ->first();

    // Store shift data in session for later retrieval
    if ($shift) {
        session(['user_shift' => $shift]);
    }
        
    if (!$shift) {
        auth()->logout();
        
        // Cek apakah user punya shift aktif tapi diluar jam
        $activeShift = \App\Models\UserShift::join('shifts', 'shifts.id', '=', 'user_shifts.shift_id')
            ->where('user_shifts.is_active', true)
            ->where('user_shifts.user_id', $user->idUser)
           // ->where('shifts.is_active', true)
            ->select('shifts.name', 'shifts.start_time', 'shifts.end_time')
            ->first();
            
        if ($activeShift) {
            return back()->with('error', 
                "Shift {$activeShift->name} hanya bisa diakses dari jam {$activeShift->start_time} sampai {$activeShift->end_time}. " .
                "Saat ini jam: " . now()->format('H:i') . "."
            );
        } else {
            return back()->with('error', 'Tidak ada shift aktif untuk user ini. Hubungi admin.');
        }
    }
}
		
			return redirect('/')->with('success', 'Sukses Login');
		} else {
			return back()->with('error', 'Password Salah');
		}
	}

	public function logout(): RedirectResponse
	{
		$this->auth->logout();

		return redirect('login')->with('error', 'Sukses Logout');
	}

}
