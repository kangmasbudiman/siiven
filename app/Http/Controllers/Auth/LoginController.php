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

        // ✅ SUPERADMIN LANGSUNG MASUK TANPA SHIFT CHECK
        if ($user->hakAkses === '1') {
            return redirect('/')->with('success', 'Sukses Login (Superadmin)');
        }

        // ✅ APPROVER (Kabag/Direktur/Ka.Keuangan/Bendahara) LANGSUNG MASUK TANPA SHIFT
        if ($user->approval_level) {
            return redirect('/')->with('success', 'Sukses Login (' . ['','Pemeriksa','Direktur','Ka. Keuangan','Bendahara'][$user->approval_level] . ')');
        }

        // ✅ DETEKSI SHIFT BERDASARKAN JAM SAAT LOGIN
        $currentTime = now()->format('H:i:s');

        $shift = \App\Models\Shift::where('is_active', 1)
            ->whereTime('start_time', '<=', $currentTime)
            ->whereTime('end_time', '>=', $currentTime)
            ->first();

        // ✅ JIKA KETEMU SHIFT
        if ($shift) {

            // Simpan shift ke session
            session([
                'user_shift' => (object)[
                    'shift' => $shift,
                    'user' => $user
                ]
            ]);

            return redirect('/')->with('success', 'Berhasil login & masuk shift: ' . $shift->name);
        }

        // ❌ JIKA TAK ADA SHIFT YANG SESUAI
        auth()->logout();
        return back()->with('error', 'Tidak ada shift yang aktif pada jam ini. Hubungi admin.');
    }

    return back()->with('error', 'Password Salah');

    }

	public function logout(): RedirectResponse
	{
		$this->auth->logout();

		return redirect('login')->with('error', 'Sukses Logout');
	}

}
