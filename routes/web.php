<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function ()
{
	Route::get('/', 'HomeController@index')->name('home')->middleware('dashboard');
	
	Route::patch('/change_password', 'UserController@updatePassword')->name('change_password');
	Route::view('/change_password', 'change_password');

	Route::middleware('can:isAdmin')->group(function ()
	{
		Route::get('/stuff/printall', 'StuffController@printall')->name('stuff.printall');
		Route::delete('/stuff/batch/destroy', 'StuffController@destroyBatch')->name('stuff.destroy-batch');
		Route::prefix('/ppn')->name('ppn.')->group(function ()
		{
			Route::get('/', 'PpnController@index')->name('index');
			Route::get('/count', 'PpnController@count')->name('count');
			Route::get('/export', 'PpnController@export')->name('export');
			Route::get('/print', 'PpnController@print')->name('print');
			Route::post('/datatables', 'PpnController@datatables')->name('datatables');
			Route::post('/store', 'PpnController@store')->name('store');
			Route::post('/import', 'PpnController@import')->name('import');
			Route::delete('/{ppn}/destroy', 'PpnController@destroy')->name('destroy');
		});



//project inventory


				//ruangan
							Route::prefix('/ruangan')->name('ruangan.')->group(function ()
							{
								Route::get('/', 'RuanganController@index')->name('index');
								Route::post('/store', 'RuanganController@store')->name('store');
								Route::put('/{id}', 'RuanganController@update')->name('update');
								Route::delete('/{ruangan}/destroy', 'RuanganController@destroy')->name('destroy');
							});
				//end

				//kondisi
							Route::prefix('/kondisi')->name('kondisi.')->group(function ()
							{
								Route::get('/', 'KondisiController@index')->name('index');
								Route::post('/store', 'KondisiController@store')->name('store');
								Route::put('/{id}', 'KondisiController@update')->name('update');
								Route::delete('/{kondisi}/destroy', 'KondisiController@destroy')->name('destroy');
							});
				//end
				//kategori
							Route::prefix('/kategori')->name('kategori.')->group(function ()
							{
								Route::get('/', 'KategoriController@index')->name('index');
								Route::post('/store', 'KategoriController@store')->name('store');
								Route::put('/{id}', 'KategoriController@update')->name('update');
								Route::delete('/{kategori}/destroy', 'KategoriController@destroy')->name('destroy');
							});
				//end
				//kondisi

							Route::prefix('/kondisis')->name('kondisis.')->group(function ()
							{
								Route::get('/', 'KondisiController@index')->name('index');
								Route::post('/store', 'KondisiController@store')->name('store');
								Route::put('/{id}', 'KondisiController@update')->name('update');
								Route::delete('/{kondisi}/destroy', 'KondisiController@destroy')->name('destroy');
							});


				//end

				//laporan stok barang per ruangan
							Route::prefix('/laporan')->name('laporan.')->group(function ()
							{
								Route::get('/', 'LaporaninventarisController@index')->name('index');
								Route::get('/pdf', 'LaporaninventarisController@pdf')->name('pdf');
								Route::get('/excel', 'LaporaninventarisController@excel')->name('excel');




								Route::post('/store', 'LaporaninventarisController@store')->name('store');
								Route::put('/{id}', 'LaporaninventarisController@update')->name('update');
								Route::delete('/{laporan}/destroy', 'LaporaninventarisController@destroy')->name('destroy');
							});
				//end





//end




		Route::prefix('/shift')->name('shift.')->group(function ()
		{
			Route::get('/', 'ShiftController@index')->name('index');
			Route::post('/store', 'ShiftController@store')->name('store');
			Route::put('/{id}', 'ShiftController@update')->name('update');
			Route::delete('/{shift}/destroy', 'ShiftController@destroy')->name('destroy');
		});


		Route::prefix('/aplikasi')->name('aplikasi.')->group(function ()
		{
			Route::get('/', 'AplikasiController@index')->name('index');
			Route::post('/store', 'AplikasiController@store')->name('store');
			Route::put('/{id}', 'AplikasiController@update')->name('update');
			Route::delete('/{aplikasi}/destroy', 'AplikasiController@destroy')->name('destroy');
		});


		Route::prefix('/bank')->name('bank.')->group(function ()
		{
			Route::get('/', 'BankController@index')->name('index');
				
			Route::post('/store', 'BankController@store')->name('store');
			Route::put('/{id}', 'BankController@update')->name('update');
			Route::delete('/{bank}/destroy', 'BankController@destroy')->name('destroy');
			Route::patch('/{bank}/toggle-status', 'BankController@toggleStatus')->name('toggle-status');
		});

		Route::prefix('/reseller')->name('reseller.')->group(function ()
		{
			Route::get('/', 'ResellerController@index')->name('index');
			Route::post('/store', 'ResellerController@store')->name('store');
			//Route::put('/{id}', 'ResellerController@update')->name('update');
			Route::put('/{reseller}', 'ResellerController@update')->name('update');
			Route::delete('/{reseller}/destroy', 'ResellerController@destroy')->name('destroy');
			Route::patch('/{reseller}/toggle-status', 'ResellerController@toggleStatus')->name('toggle-status');
			Route::get('/{reseller}/details', 'ResellerController@getResellerDetails')->name('details');
		});


		





			Route::prefix('/usershift')->name('usershift.')->group(function ()
		{
			Route::get('/', 'UserShiftController@index')->name('index');
			Route::post('/store', 'UserShiftController@store')->name('store');
			Route::put('/{id}', 'UserShiftController@update')->name('update');
			Route::delete('/{usershift}/destroy', 'UserShiftController@destroy')->name('destroy');
			Route::patch('/{usershift}/toggleStatus', 'UserShiftController@toggleStatus')->name('toggleStatus');
		});












		Route::prefix('/user')->name('user.')->group(function ()
		{
			Route::post('/datatables', 'UserController@datatables')->name('datatables');
			Route::post('/select', 'UserController@select')->name('select');
		});

		Route::prefix('/stock')->name('stock.')->group(function ()
		{
			Route::get('/printall', 'StockController@printall')->name('printall');
			Route::get('/export', 'StockController@export')->name('export');
			Route::post('/import', 'StockController@import')->name('import');
			Route::delete('/{id}', 'StockController@destroy')->name('destroy');
		});

		Route::prefix('/detail_stock')->name('detail_stock.')->group(function ()
		{
			Route::get('/', 'StockController@detailStock')->name('index');
			Route::get('/printall', 'StockController@detailPrintall')->name('printall');
			Route::get('/export', 'StockController@detailExport')->name('export');
			Route::post('/import', 'StockController@detailImport')->name('import');
			Route::post('/datatables', 'StockController@detailDatatables')->name('datatables');
		});

		Route::prefix('/opname')->name('opname.')->group(function ()
		{
			Route::get('/export', 'OpnameController@export')->name('export');
			Route::get('/print', 'OpnameController@print')->name('print');
			Route::post('/datatables', 'OpnameController@datatables')->name('datatables');
			Route::post('/import', 'OpnameController@import')->name('import');
		});

	
	
		Route::prefix('/setting')->name('setting.')->group(function ()
		{
			Route::put('/', 'SettingController@update')->name('update');
			Route::get('/', 'SettingController@index')->name('index');
		});
	});

	Route::middleware('can:isAdminGudang')->group(function ()
	{
	
		Route::get('/notification', 'HomeController@notification')->name('notification');

		Route::prefix('/barcode')->name('barcode.')->group(function ()
		{
			Route::view('/search', 'barcode.search')->name('search');
			Route::get('/print', 'BarcodeController@print')->name('print');
		});

		Route::prefix('/stuff')->name('stuff.')->group(function ()
		{
			Route::get('/export', 'StuffController@export')->name('export');
			// Route::get('/printallbarcode', 'StuffController@printAllBarcode')->name('printallbarcode');
			Route::get('/barcode/{stuff}', 'StuffController@barcode')->name('barcode');
			Route::get('/barcode/{stuff}/print', 'StuffController@printBarcode')->name('barcode.print');
			Route::post('/import', 'StuffController@import')->name('import');
		});

		Route::prefix('/category')->name('category.')->group(function ()
		{
			Route::get('/export', 'CategoryController@export')->name('export');
			Route::post('/import', 'CategoryController@import')->name('import');
			Route::post('/datatables', 'CategoryController@datatables')->name('datatables');
			Route::post('/select', 'CategoryController@select')->name('select');
		});

		Route::prefix('/rack')->name('rack.')->group(function ()
		{
			Route::get('/export', 'RackController@export')->name('export');
			Route::post('/import', 'RackController@import')->name('import');
			Route::post('/datatables', 'RackController@datatables')->name('datatables');
			Route::post('/select', 'RackController@select')->name('select');
		});
		
		Route::prefix('/distributor')->name('distributor.')->group(function ()
		{
			Route::get('/export', 'DistributorController@export')->name('export');
			Route::post('/import', 'DistributorController@import')->name('import');
			Route::post('/datatables', 'DistributorController@datatables')->name('datatables');
			Route::post('/select', 'DistributorController@select')->name('select');
		});
		Route::prefix('/daylicash')->name('daylicash.')->group(function ()
		{
			Route::get('/export', 'DaylicashController@export')->name('export');
			Route::post('/import', 'DaylicashController@import')->name('import');
			Route::post('/datatables', 'DaylicashController@datatables')->name('datatables');
			Route::post('/select', 'DaylicashController@select')->name('select');
		});

		Route::prefix('/stock')->name('stock.')->group(function ()
		{
			Route::get('/', 'StockController@index')->name('index');
			
			Route::view('/create', 'stock.create')->name('create');
			Route::view('/edit/{id}', 'stock.edit')->name('edit');

			Route::get('/detail/{id}', 'StockController@detail')->name('detail');
			Route::get('/print/{id}', 'StockController@print')->name('print');

			Route::post('/datatables', 'StockController@datatables')->name('datatables');			
		});

		Route::prefix('/finance')->name('finance.')->group(function ()
		{
			Route::get('/printall', 'FinanceController@printall')->name('printall');
			Route::get('/export', 'FinanceController@export')->name('export');
			Route::post('/import', 'FinanceController@import')->name('import');
			Route::post('/datatables', 'FinanceController@datatables')->name('datatables');
		});

		//new cash harian
		Route::prefix('/cash')->name('cash.')->group(function ()
		{
			Route::get('/printall', 'CashController@printall')->name('printall');
			Route::get('/export', 'CashController@export')->name('export');
			Route::post('/import', 'CashController@import')->name('import');
			Route::post('/datatables', 'CashController@datatables')->name('datatables');
		});



		Route::prefix('/category_finance')->name('category_finance.')->group(function ()
		{
			Route::post('/datatables', 'CategoryFinanceController@datatables')->name('datatables');
			Route::post('/select', 'CategoryFinanceController@select')->name('select');
		});
	});
	
	Route::middleware('can:isAdminKasir')->group(function ()
	{

// untuk aplikasi inventori RS
			

//data barang

	                    	Route::prefix('/barang')->name('barang.')->group(function ()
							{
								Route::get('/', 'BarangController@index')->name('index');
								Route::post('/store', 'BarangController@store')->name('store');
								Route::put('/{id}', 'BarangController@update')->name('update');
								Route::delete('/{barang}/destroy', 'BarangController@destroy')->name('destroy');
							});

							 	Route::prefix('/inventory')->name('inventory.')->group(function ()
							{
								Route::get('/', 'InventoryController@index')->name('index');
								Route::post('/store', 'InventoryController@store')->name('store');
								Route::put('/{id}', 'InventoryController@update')->name('update');
								Route::delete('/{inventory}/destroy', 'InventoryController@destroy')->name('destroy');
							});



//




		Route::prefix('/transaction')->name('transaction.')->group(function ()
		{
			Route::get('/', 'TransactionController@index')->name('index');
			Route::view('/create', 'transaction.create')->name('create');
			
			Route::post('/datatables', 'TransactionController@datatables')->name('datatables');
			
			Route::get('/detail/{id}', 'TransactionController@detail')->name('detail');
			Route::get('/print/{id}', 'TransactionController@print')->name('print');
			Route::patch('/cancel/{id}', 'TransactionController@cancel')->name('cancel');
			Route::post('/store', 'TransactionController@store')->name('store');
			Route::post('/update', 'TransactionController@update')->name('update');
			Route::delete('/{id}/destroy', 'TransactionController@destroy')->name('destroy');
			Route::post('/approve/{id}', 'TransactionController@approve')->name('approve');
			Route::get('/laporanperiode', 'TransactionController@laporanperiode')->name('laporanperiode');
			Route::get('/laporanperiodePdf', 'TransactionController@laporanPeriodePdf')->name('laporanPeriodePdf');
			Route::get('/reload', 'TransactionController@reload')->name('reload');
			Route::post('/reload/store', 'TransactionController@reloadStore')->name('reloadStore');
			
		});


		Route::prefix('/stockapp')->name('stockapp.')->group(function ()
		{
				Route::get('/', 'StockAppController@index')->name('index');
				Route::get('/admin', 'StockAppController@indexadmin')->name('indexadmin');
				Route::get('/pdf', 'StockAppController@exportPdf')->name('pdf');
			Route::post('/reload', 'StockAppController@reload')->name('reload');
		});



	});


Route::prefix('/shiftsession')->name('shiftsession.')->group(function ()
		{
			Route::get('/', 'ShiftSessionController@index')->name('index');
			Route::post('/store', 'ShiftSessionController@store')->name('store');
			Route::put('/{id}', 'ShiftSessionController@update')->name('update');
			Route::delete('/{shiftsession}/destroy', 'ShiftSessionController@destroy')->name('destroy');
			Route::patch('/{shiftsession}/toggleStatus', 'ShiftSessionController@toggleStatus')->name('toggleStatus');
			Route::post('/start', 'ShiftSessionController@start')->name('start');
			Route::post('/end', 'ShiftSessionController@end')->name('end');
			Route::post('/approve/{id}', 'ShiftSessionController@approve')->name('approve');
			Route::get('/history', 'ShiftSessionController@history')->name('history');
			Route::get('/view/{id}', 'ShiftSessionController@view')->name('view');
			Route::post('/join', 'ShiftSessionController@joinShift')->name('join');
			Route::post('/leave', 'ShiftSessionController@leaveShift')->name('leave');
			Route::post('/rejoin', 'ShiftSessionController@rejoin')->name('rejoin');
			Route::get('/detailshift/{id}', 'ShiftSessionController@detailshift')->name('detailshift');
			Route::get('/report/{id}', 'ShiftSessionController@report')->name('report');
			Route::get('/listshiftsession', 'ShiftSessionController@listshiftsession')->name('listshiftsession');	
			Route::get('/pdf/{id}', 'ShiftSessionController@getpdf')->name('pdf');
				
		});





	Route::prefix('/stuff')->name('stuff.')->group(function ()
	{
		Route::post('/datatables', 'StuffController@datatables')->name('datatables');
		Route::post('/select', 'StuffController@select')->name('select');
	});
	
	Route::prefix('/report')->name('report.')->group(function ()
	{
		Route::middleware('can:isAdmin')->group(function ()
		{
			Route::get('/expend', 'ReportController@expend')->name('expend');
			Route::get('/accumulation', 'ReportController@accumulation')->name('accumulation');
			Route::get('/accumulation/new', 'ReportController@accumulationNew')->name('accumulation.new');
			Route::get('/transaction', 'ReportController@transaction')->name('transaction');
			Route::get('/sell', 'ReportController@sell')->name('sell');
			Route::get('/stock', 'ReportController@stock')->name('stock');
			Route::get('/buy', 'ReportController@buy')->name('buy');
			Route::get('/print/expend', 'ReportController@printExpend')->name('expend.print');
			Route::get('/print/accumulation', 'ReportController@printAccumulation')->name('accumulation.print');
			Route::get('/print/accumulation/new', 'ReportController@printAccumulationNew')->name('accumulation.print.new');
			Route::get('/print/transaction', 'ReportController@printTransaction')->name('transaction.print');
			Route::get('/print/sell', 'ReportController@printSell')->name('sell.print');
			Route::get('/print/stock', 'ReportController@printStock')->name('stock.print');
			Route::get('/print/buy', 'ReportController@printBuy')->name('buy.print');
		});

		Route::middleware('can:isAdminKasir')->group(function ()
		{
			Route::get('/transaction/today', 'ReportController@transactionToday')->name('transaction.today');
			Route::get('/transaction/month', 'ReportController@transactionMonth')->name('transaction.month');
			Route::get('/print/transaction/today', 'ReportController@printTransactionToday')->name('transaction.print.today');
			Route::get('/print/transaction/month', 'ReportController@printTransactionMonth')->name('transaction.print.month');
			Route::get('/sell/today', 'ReportController@sellToday')->name('sell.today');
			Route::get('/sell/month', 'ReportController@sellMonth')->name('sell.month');
			Route::get('/print/sell/today', 'ReportController@printSellToday')->name('sell.print.today');
			Route::get('/print/sell/month', 'ReportController@printSellMonth')->name('sell.print.month');
		});

		Route::middleware('can:isAdminGudang')->group(function ()
		{
			Route::get('/stock/today', 'ReportController@stockToday')->name('stock.today');
			Route::get('/stock/month', 'ReportController@stockMonth')->name('stock.month');
			Route::get('/buy/today', 'ReportController@buyToday')->name('buy.today');
			Route::get('/buy/month', 'ReportController@buyMonth')->name('buy.month');
			Route::get('/print/stock/today', 'ReportController@printStockToday')->name('stock.print.today');
			Route::get('/print/stock/month', 'ReportController@printStockMonth')->name('stock.print.month');
			Route::get('/print/buy/today', 'ReportController@printBuyToday')->name('buy.print.today');
			Route::get('/print/buy/month', 'ReportController@printBuyMonth')->name('buy.print.month');
		});

	});

	// Usulan Pembelian Barang (semua role yang sudah login)
	Route::prefix('/usulan-pembelian')->name('usulan.')->group(function () {
		Route::get('/', 'UsulanPembelianController@index')->name('index');
		Route::get('/data', 'UsulanPembelianController@data')->name('data');
		Route::get('/create', 'UsulanPembelianController@create')->name('create');
		Route::post('/store', 'UsulanPembelianController@store')->name('store');
		Route::get('/{id}', 'UsulanPembelianController@show')->name('show');
		Route::get('/{id}/edit', 'UsulanPembelianController@edit')->name('edit');
		Route::put('/{id}', 'UsulanPembelianController@update')->name('update');
		Route::delete('/{id}', 'UsulanPembelianController@destroy')->name('destroy');
		Route::post('/{id}/submit', 'UsulanPembelianController@submit')->name('submit');
		Route::post('/{id}/approve', 'UsulanPembelianController@approve')->name('approve');
		Route::post('/{id}/reject', 'UsulanPembelianController@reject')->name('reject');
		Route::post('/{id}/resubmit', 'UsulanPembelianController@resubmit')->name('resubmit');
		Route::post('/{id}/reject-item/{detailId}', 'UsulanPembelianController@rejectItem')->name('reject-item');
		Route::post('/{id}/restore-item/{detailId}', 'UsulanPembelianController@restoreItem')->name('restore-item');
		Route::get('/{id}/pdf', 'UsulanPembelianController@pdf')->name('pdf');
		Route::get('/{id}/wa', 'UsulanPembelianController@waLink')->name('wa');
	});

	Route::resource('/category', 'CategoryController')->except(['create', 'edit', 'show'])->middleware('can:isAdminGudang');
	Route::resource('/rack', 'RackController')->except(['create', 'edit', 'show'])->middleware('can:isAdminGudang');
	Route::resource('/daylicash', 'DaylicashController')->except([ 'edit', 'show'])->middleware('can:isAdminGudang');
	Route::resource('/distributor', 'DistributorController')->except(['edit', 'show'])->middleware('can:isAdminGudang');
	Route::resource('/stuff', 'StuffController')->except(['edit'])->except(['edit'])->middleware('can:isAdminGudang');
	Route::resource('/user', 'UserController')->except(['edit', 'show'])->middleware('can:isAdmin');
	Route::resource('/finance', 'FinanceController')->except(['create', 'edit', 'show'])->middleware('can:isAdmin');
	Route::resource('/cash', 'CashController')->except(['create', 'edit', 'show'])->middleware('can:isAdmin');
	Route::resource('/category_finance', 'CategoryFinanceController')->except(['create', 'edit', 'show'])->middleware('can:isAdmin');
	Route::resource('/opname', 'OpnameController')->except(['show', 'update'])->middleware('can:isAdmin');
});

Route::namespace('Auth')->group(function ()
{
	Route::get('/login', 'LoginController@showLoginForm');
	Route::post('/login', 'LoginController@login')->name('login');

	Route::get('/logout', 'LoginController@logout')->name('logout');
});

// Public: Verifikasi QR Code tanda tangan digital (tanpa login)
Route::get('/verify/{token}', 'VerifikasiController@verify')->name('verify');

// API: cek jumlah pending approval (untuk polling notifikasi suara)
Route::middleware('auth')->get('/api/pending-approval-count', function () {
    $user = auth()->user();
    if (!$user->approval_level) {
        return response()->json(['count' => 0, 'items' => []]);
    }
    $statusMap = [1 => 'diajukan', 2 => 'diperiksa', 3 => 'dikonfirmasi', 4 => 'diketahui'];
    $status = $statusMap[$user->approval_level] ?? null;
    $count = 0;
    $items = [];
    if ($status) {
        $query = \App\Models\UsulanPembelian::with('ruangan')->where('status', $status);

        // Level 1: hanya tampilkan usulan dari ruangan yang ditunjuk ke user ini
        if ($user->approval_level == 1) {
            $query->whereHas('ruangan', function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('approver_id', $user->idUser)->orWhereNull('approver_id');
                });
            });
        }

        $count   = (clone $query)->count();
        $usulans = $query->latest()->limit(5)->get();
        $items = $usulans->map(fn($u) => [
            'id'      => $u->id,
            'nomor'   => $u->nomor_usulan,
            'ruangan' => $u->ruangan->nama_ruangan ?? '-',
            'tanggal' => $u->tanggal_pengajuan->format('d/m/Y'),
            'url'     => route('usulan.show', $u->id),
        ]);
    }
    return response()->json(['count' => $count, 'items' => $items]);
})->name('api.pending.count');