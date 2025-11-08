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


class HomeController extends Controller
{

	public function index(Request $request): View
	{
	
		$today = today();
$userId = auth()->id();

$totalTransactionsToday = Transaction::whereDate('created_at', $today)->count();

$totalOutstanding = Transaction::where('status', 'PENDING')
    ->sum('outstanding_amount');

$totalResellers = Reseller::where('is_active', 1)->count();

$totalBankAccounts = Bank::count();
$activeShift= ShiftSession::where('status', 'ACTIVE')->get();

//
$joinedShift= ShiftSession::count();
$applications= Aplikasi::count();
$pendingTransactions= Transaction::where('status', 'PENDING')->count();
$completedTransactions= Transaction::where('status', 'COMPLETED')->count();
$newTransactions= Transaction::where('status', 'NEW')->count();
$bankAccounts= Bank::count();
$resellers= Reseller::count();
$recentTransactions = Transaction::orderBy('created_at', 'desc')
    ->take(10)
    ->get();
$chartData = \App\Models\Transaction::whereDate('created_at', '>=', now()->subDays(6))
    ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
    ->groupBy('date')
    ->orderBy('date', 'asc')
    ->pluck('total', 'date');







return view('home', compact(
    'activeShift',
    'joinedShift',
    'applications',
    'pendingTransactions',
    'completedTransactions',
    'newTransactions',
    'bankAccounts',
    'resellers',
    'totalTransactionsToday',
    'totalOutstanding',
    'totalResellers',
    'totalBankAccounts',
	'recentTransactions',
	'chartData',
	
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

		$jatuhtempo=Stock::whereRaw('DATEDIFF(jatuh_tempo,current_date) <= nobatch')->get();
		

		$hitung_jatuhtempo=Stock::whereRaw('DATEDIFF(jatuh_tempo,current_date) <= nobatch')->count();
		
		
		
		



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
