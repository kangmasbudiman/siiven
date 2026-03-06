<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Services\StockService;
use App\Services\ExpendService;
use App\Models\Transaction;
use App\Models\Reseller;
use App\Models\Bank;
use App\Models\ShiftSession;
use App\Models\Aplikasi;
use App\Repositories\StuffRepository;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Ruangan;
use App\Models\Barang;
use App\Models\StockBarang;
use App\Models\UsulanPembelian;
use App\Models\ApprovalUsulanPembelian;


class HomeController extends Controller
{

	public function index(Request $request): View
	{
		$user = auth()->user();

		// Dashboard khusus untuk approver (Kabag, Direktur, Ka.Keuangan, Bendahara)
		if ($user->approval_level) {
			return $this->approverDashboard($user);
		}

		$today = today();
		$userId = auth()->id();

		$totalRuangan = Ruangan::count();
		$totalBarang = Barang::count();
		$totalStok = StockBarang::sum('jumlah');

		$barangRusak = StockBarang::whereHas('kondisi', function ($q) {
			$q->where('nama_kondisi', '!=', 'Baik');
		})->sum('jumlah');

		// Grafik stok per ruangan
		$stokPerRuangan = StockBarang::select(
				'ruangans.nama_ruangan',
				DB::raw('SUM(stock_barangs.jumlah) as total')
			)
			->join('ruangans', 'ruangans.id', '=', 'stock_barangs.ruangan_id')
			->groupBy('ruangans.nama_ruangan')
			->get();

		// Ruangan stok menipis
		$stokMenipis = StockBarang::with('ruangan', 'barang')
			->where('jumlah', '<=', 5)
			->orderBy('jumlah')
			->limit(10)
			->get();

		return view('home', compact(
			'today',
			'userId',
			'totalRuangan',
			'totalBarang',
			'totalStok',
			'barangRusak',
			'stokPerRuangan',
			'stokMenipis',
		));
	}

	private function approverDashboard($user): View
	{
		$roleLabels = [
			1 => 'Pemeriksa / Kabag',
			2 => 'Konfirmator / Direktur',
			3 => 'Ka. Keuangan',
			4 => 'Bendahara',
		];

		$pendingStatusMap = [
			1 => 'diajukan',
			2 => 'diperiksa',
			3 => 'dikonfirmasi',
			4 => 'diketahui',
		];

		$roleLabel     = $roleLabels[$user->approval_level] ?? 'Approver';
		$pendingStatus = $pendingStatusMap[$user->approval_level] ?? null;

		$pendingUsulans = $pendingStatus
			? UsulanPembelian::with(['ruangan', 'pembuat'])
				->where('status', $pendingStatus)
				->latest()
				->get()
			: collect();

		$totalPending  = $pendingUsulans->count();
		$totalApproved = ApprovalUsulanPembelian::where('user_id', $user->idUser)
			->where('status', 'approved')->count();
		$totalRejected = ApprovalUsulanPembelian::where('user_id', $user->idUser)
			->where('status', 'rejected')->count();

		// Riwayat persetujuan terbaru oleh user ini
		$riwayatApproval = ApprovalUsulanPembelian::with(['usulan.ruangan'])
			->where('user_id', $user->idUser)
			->latest('approved_at')
			->limit(5)
			->get();

		return view('home-approver', compact(
			'roleLabel',
			'pendingUsulans',
			'totalPending',
			'totalApproved',
			'totalRejected',
			'riwayatApproval',
		));
	}

	public function notification(StuffRepository $stuffRepo): View
	{
		$stuffs = $stuffRepo->getLimit();

		return view('notification', compact('stuffs'));
	}

	public function data(): Array
	{
		$transaction = app(TransactionService::class);
		$stockService = app(StockService::class);
		$expendService = app(ExpendService::class);

		$buyActivity = $stockService->latest(date('Y-m-d'));
		$expendActivity = $expendService->filter(date('Y-m-d'));
		$cancelActivity = $transaction->filterCancel(date('Y-m-d'));
		$transactionActivity = $transaction->filterSuccess(date('Y-m-d'));

		$jatuhtempo = Stock::whereRaw('DATEDIFF(jatuh_tempo,current_date) <= nobatch')->get();
		$hitung_jatuhtempo = Stock::whereRaw('DATEDIFF(jatuh_tempo,current_date) <= nobatch')->count();

		return [
			'buyActivity' => $buyActivity,
			'expendActivity' => $expendActivity,
			'cancelActivity' => $cancelActivity,
			'transactionActivity' => $transactionActivity,
			'mantap' => $jatuhtempo,
			'totaljatuhtempo' => $hitung_jatuhtempo,
		];
	}

}
