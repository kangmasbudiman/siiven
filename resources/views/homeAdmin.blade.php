@extends('_layouts.app')

@section('title', 'Dashboard Admin')

@section('content')



    <div class="container-fluid mt-3">

        {{-- ============================== --}}
        {{-- INFORMASI SHIFT PENGGUNA --}}
        {{-- ============================== --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">👤 Informasi Shift Pengguna</h5>
                <table class="table table-bordered mb-0">
                    <tr>
                        <th>Nama</th>
                        <td>{{ $user_shift->user->nama ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi</th>
                        <td>{{ $user_shift->user->lokasi ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>{{ ucfirst($user_shift->user->roleadmin ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <th>Data Shift</th>
                        <td>
                            @if ($user_shift->shift)
                                <span class="badge badge-info">{{ $user_shift->shift->name }}</span>
                                <small class="text-muted d-block">
                                    {{ $user_shift->shift->start_time->format('H:i') }} -
                                    {{ $user_shift->shift->end_time->format('H:i') }}
                                </small>
                            @else
                                <span class="text-muted">Tidak ada shift</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ============================== --}}
        {{-- SHIFT SEDANG AKTIF --}}
        {{-- ============================== --}}



        @if (!$activeShift)

            @if (!$activeShift && $lastJoinedShift && $lastJoinedShift->session->status === 'ACTIVE')
                <div class="alert alert-info text-center">
                    Kamu sebelumnya berada di shift milik
                    <strong>{{ $lastJoinedShift->session->openedBy->nama }}</strong>.
                </div>

                <form method="POST" action="{{ route('shiftsession.rejoin') }}" class="text-center mb-4">
                    @csrf
                    <input type="hidden" name="shift_id" value="{{ $lastJoinedShift->session->id }}">
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-recycle"></i> Gabung Kembali ke Shift Terakhir
                    </button>
                </form>
            @endif




            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">🚫 Belum Ada Shift Aktif</h4>
                    <p class="text-muted">Mulai shift baru untuk memulai aktivitas hari ini.</p>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#startShiftModal">
                        <i class="fa fa-play-circle"></i> Mulai Shift Sekarang
                    </button>
                </div>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🕒 Shift Sedang Aktif</h5>
                    <div>
                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalAddTransaction">
                            <i class="fa fa-plus-circle"></i> Tambah Transaksi
                        </button>
                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirmEndShiftModal"
                            data-shift-id="{{ $activeShift->id }}">
                            <i class="fa fa-stop-circle"></i> Tutup Shift
                        </button>
                    </div>
                </div>


                @if ($joinedShift && $joinedShift->session->opened_by != auth()->id())
                    <div class="alert alert-info d-flex justify-content-between align-items-center mb-0">
                        <div>
                            <i class="fa fa-users"></i> Kamu sedang bergabung di shift milik
                            <strong>{{ $joinedShift->session->openedBy->nama }}</strong>.
                        </div>
                        <form method="POST" action="{{ route('shiftsession.leave') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fa fa-sign-out-alt"></i> Keluar dari Shift
                            </button>
                        </form>
                    </div>
                @endif






                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Kode Shift</th>
                            <td>{{ $activeShift->session_code }}</td>
                        </tr>
                        <tr>
                            <th>Dibuka Oleh</th>
                            <td>{{ $activeShift->openedBy->nama ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Waktu Mulai</th>
                            <td>{{ $activeShift->start_time->format('d M Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Saldo Awal</th>
                            <td>Rp {{ number_format($activeShift->opening_balance, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><span class="badge badge-success">Aktif</span></td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $activeShift->note }}</td>
                        </tr>
                    </table>
                    <hr>
                    <hr>
                    @if ($activeShift)
                        @php
                            $joined = \App\Models\ShiftMember::where('shift_session_id', $activeShift->id)
                                ->where('user_id', auth()->id())
                                ->exists();
                        @endphp

                        @if (!$joined)
                            <form method="POST" action="{{ route('shiftsession.join') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-info">
                                    <i class="fa fa-users"></i> Join Shift
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary" disabled>
                                <i class="fa fa-check"></i> Kamu sudah bergabung
                            </button>
                        @endif
                    @endif

                    {{-- ============================== --}}
                    {{-- TAB MENU --}}
                    {{-- ============================== --}}
                    <ul class="nav nav-tabs mt-4" id="shiftTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-new" data-toggle="tab" href="#transaksiBaru"
                                role="tab">🧾 Transaksi Baru</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-pending" data-toggle="tab" href="#pendingExec" role="tab">⏳
                                Pending Exec</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-history" data-toggle="tab" href="#riwayat" role="tab">✅ Riwayat
                                Hari Ini</a>
                        </li>
                    </ul>

                    <div class="tab-content border p-3" id="shiftTabsContent">
                        {{-- TAB TRANSAKSI BARU --}}
                        <div class="tab-pane fade show active text-center" id="transaksiBaru" role="tabpanel">
                            @if ($newTransactions->count() > 0)
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Customer Type</th>
                                                <th>Aplikasi</th>
                                                <th>Qty</th>
                                                <th>Amount Due</th>
                                                <th>Status</th>
                                                <th>Notes</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($newTransactions as $trx)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $trx->customer_type }}</td>
                                                    <td>{{ $trx->application->nameaplication ?? '-' }}</td>
                                                    <td>{{ $trx->coin_qty }}</td>
                                                    <td>Rp {{ number_format($trx->amount_due, 0, ',', '.') }}</td>
                                                    <td><span class="badge badge-primary">{{ $trx->status }}</span></td>
                                                    <td>{{ $trx->notes_transaction }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning btn-edit-transaction"
                                                            data-toggle="modal" data-target="#modalEditTransaction"
                                                            data-id="{{ $trx->id }}"
                                                            data-app-id="{{ $trx->app_id }}"
                                                            data-rate="{{ $trx->rate }}"
                                                            data-coin-type="{{ $trx->coin_type }}"
                                                            data-amount-due="{{ $trx->amount_due }}"
                                                            data-amount-paid="{{ $trx->amount_paid }}"
                                                            data-outstanding="{{ $trx->outstanding_amount }}"
                                                            data-payment-type="{{ $trx->payment_type }}"
                                                            data-payment-method="{{ $trx->payment_method }}"
                                                            data-payment-account-id="{{ $trx->payment_account_id }}"
                                                            data-customer-type="{{ $trx->customer_type }}"
                                                            data-customer-phone="{{ $trx->customer_phone }}"
                                                            data-reseller-id="{{ $trx->reseller_id }}"
                                                            data-notes-transaction="{{ $trx->notes_transaction }}"
                                                            data-coin-qty="{{ $trx->coin_qty }}" <i
                                                            class="fa fa-edit"></i>
                                                            Edit
                                                        </button>

                                                        <button class="btn btn-sm btn-danger btn-delete-transaction"
                                                            data-toggle="modal"
                                                            data-target="#confirmDeleteTransactionModal"
                                                            data-id="{{ $trx->id }}">
                                                            <i class="fa fa-trash"></i> Hapus
                                                        </button>



                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="mt-3">Gunakan tombol <strong>Tambah Transaksi</strong> di atas untuk input
                                    transaksi baru.</p>
                            @endif
                        </div>

                        {{-- TAB PENDING EXEC --}}
                        <div class="tab-pane fade" id="pendingExec" role="tabpanel">
                            <h6 class="mt-3">Daftar Transaksi Pending Eksekusi</h6>
                            <table class="table table-striped table-sm mt-2">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Aplikasi</th>
                                        <th>Qty</th>
                                        <th>Amount Due</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pendingTransactions as $trx)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $trx->customer_type }}</td>
                                            <td>{{ $trx->application->nameaplication ?? '-' }}</td>
                                            <td>{{ $trx->coin_qty }}</td>
                                            <td>Rp {{ number_format($trx->amount_due, 0, ',', '.') }}</td>
                                            <td><span class="badge badge-warning">{{ $trx->status }}</span></td>
                                            <td class="d-flex gap-1">
                                                @if (Auth::user()->roleadmin === 'execute')
                                                    <button class="btn btn-sm btn-success btn-approve-transaction"
                                                        data-toggle="modal" data-target="#confirmApproveTransactionModal"
                                                        data-id="{{ $trx->id }}">
                                                        <i class="fa fa-check"></i> Approve
                                                    </button>
                                                @endif

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Tidak ada transaksi pending.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- TAB RIWAYAT --}}
                        <div class="tab-pane fade" id="riwayat" role="tabpanel">
                            <h6 class="mt-3">Riwayat Transaksi Hari Ini</h6>
                            <table class="table table-striped table-sm mt-2">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Customer</th>
                                        <th>Aplikasi</th>
                                        <th>Qty</th>
                                        <th>Amount Due</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($completedTransactions as $trx)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $trx->customer_type }}</td>
                                            <td>{{ $trx->application->nameaplication ?? '-' }}</td>
                                            <td>{{ $trx->coin_qty }}</td>
                                            <td>Rp {{ number_format($trx->amount_due, 0, ',', '.') }}</td>
                                            <td><span class="badge badge-success">{{ $trx->status }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada transaksi selesai.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        @endif
    </div>


    {{-- ============================== --}}
    {{-- MODAL START SHIFT --}}
    {{-- ============================== --}}
    <div class="modal fade" id="startShiftModal" tabindex="-1" aria-labelledby="startShiftModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">

            <form method="POST" action="{{ route('shiftsession.start') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="startShiftModalLabel">
                            <i class="fa fa-play-circle"></i> Mulai Shift Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="opening_balance">Saldo Awal (Rp)</label>
                            <input type="number" step="0.01" name="opening_balance" id="opening_balance"
                                class="form-control" required placeholder="Masukkan saldo awal kas">
                        </div>
                        <div class="form-group mt-3">
                            <label for="notes">Catatan</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                placeholder="Opsional: Catatan tambahan untuk shift ini"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check-circle"></i> Mulai Shift
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>






    {{-- ============================== --}}
    {{-- MODAL TAMBAH TRANSAKSI --}}
    {{-- ============================== --}}
    <div class="modal fade" id="modalAddTransaction" tabindex="-1" aria-labelledby="modalAddTransactionLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('transaction.store') }}" id="formTransaction">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fa fa-plus-circle"></i> Input Transaksi Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        {{-- APLIKASI --}}
                        <div class="row">
                            <div class="col-md-6">
                                <label>Aplikasi</label>
                                <select name="app_id" id="app_id" class="form-control" required>
                                    <option value="">-- Pilih Aplikasi --</option>
                                    @foreach ($applications as $app)
                                        <option value="{{ $app->id }}" data-rate="{{ $app->rate }}"
                                            data-coin-type="{{ $app->coin_type }}"
                                            data-appqty="{{ $app->qty }}"
                                            >
                                            
                                            {{ $app->nameaplication }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Coin Type</label>
                                <input type="text" class="form-control" name="coin_type" id="coin_type" readonly>
                            </div>
                              <div class="col-md-3">
                                <label>App Qty </label>
                                <input type="text" class="form-control" name="appqty" id="appqty" readonly>
                            </div>
                            <div class="col-md-3">
                                <label>Rate</label>
                                <input type="number" step="0.01" name="rate" id="rate" class="form-control"
                                    readonly>
                            </div>
                        </div>

                        <hr>

                        {{-- JUMLAH & PEMBAYARAN --}}
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Total (Amount Due)</label>
                                <input type="number" step="0.01" name="amount_due" id="amount_due"
                                    class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Qty (Coin Qty)</label>
                                <input type="number" step="0.01" name="coin_qty" id="coin_qty"
                                    class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Payment Type</label>
                                <select name="payment_type" id="payment_type" class="form-control" required>
                                    <option value="Lunas">Lunas</option>
                                    <option value="Kasbon">Kasbon</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Amount Paid</label>
                                <input type="number" step="0.01" name="amount_paid" id="amount_paid"
                                    class="form-control" value="0">
                            </div>
                            <div class="col-md-4">
                                <label>Outstanding Amount</label>
                                <input type="number" step="0.01" name="outstanding_amount" id="outstanding_amount"
                                    class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Transfer Bank">Transfer Bank</option>
                                    <option value="E-Wallet">E-Wallet</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label>Payment Account</label>
                                <select name="payment_account_id" id="payment_account_id" class="form-control">
                                    <option value="">-- Pilih Akun --</option>
                                    @php
                                        // Group accounts by bank name
                                        $groupedBanks = $bankAccounts->groupBy('bank_name');
                                    @endphp

                                    @foreach ($groupedBanks as $bankName => $accounts)
                                        <optgroup label="{{ $bankName }}">
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->account_number }} -
                                                    {{ $acc->account_holder }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>No. WA Customer</label>
                                <input type="text" name="customer_phone" id="customer_phone" class="form-control">
                            </div>
                        </div>

                        <hr>

                        {{-- CUSTOMER TYPE --}}
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label>Customer Type</label>
                                <select name="customer_type" id="customer_type" class="form-control" required>
                                    <option value="Non-Reseller">Non-Reseller</option>
                                    <option value="Reseller">Reseller</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Nama Reseller</label>
                                <select name="reseller_id" id="reseller_id" class="form-control" disabled>
                                    <option value="">-- Pilih Reseller --</option>
                                    @foreach ($resellers as $res)
                                        <option value="{{ $res->id }}">{{ $res->namereseller }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label>Catatan</label>
                            <textarea name="notes_transaction" class="form-control" placeholder="Contoh: kirim cepat, janji bayar malam"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>




    <!-- ============================== -->
    <!-- MODAL EDIT TRANSAKSI -->
    <!-- ============================== -->
    <div class="modal fade" id="modalEditTransaction" tabindex="-1" aria-labelledby="modalEditTransactionLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('transaction.update') }}" id="formEditTransaction">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title"><i class="fa fa-edit"></i> Edit Transaksi</h5>
                        <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        {{-- APLIKASI --}}
                        <div class="row">
                            <div class="col-md-6">
                                <label>Aplikasi</label>
                                <select name="app_id" id="edit_app_id" class="form-control" required>
                                    <option value="">-- Pilih Aplikasi --</option>
                                    @foreach ($applications as $app)
                                        <option value="{{ $app->id }}" data-rate="{{ $app->rate }}"
                                            data-coin-type="{{ $app->coin_type }}">
                                            {{ $app->nameaplication }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Coin Type</label>
                                <input type="text" name="coin_type" id="edit_coin_type" class="form-control"
                                    readonly>
                            </div>
                            <div class="col-md-3">
                                <label>Rate</label>
                                <input type="number" step="0.01" name="rate" id="edit_rate" class="form-control"
                                    readonly>
                            </div>
                        </div>

                        <hr>

                        {{-- AMOUNT --}}
                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Total (Amount Due)</label>
                                <input type="number" step="0.01" name="amount_due" id="edit_amount_due"
                                    class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Coin Qty</label>
                                <input type="number" step="0.01" name="coin_qty" id="edit_coin_qty"
                                    class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Payment Type</label>
                                <select name="payment_type" id="edit_payment_type" class="form-control" required>
                                    <option value="Lunas">Lunas</option>
                                    <option value="Kasbon">Kasbon</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4">
                                <label>Amount Paid</label>
                                <input type="number" step="0.01" name="amount_paid" id="edit_amount_paid"
                                    class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label>Outstanding</label>
                                <input type="number" step="0.01" name="outstanding_amount" id="edit_outstanding"
                                    class="form-control" readonly>
                            </div>
                            <div class="col-md-4">
                                <label>Payment Method</label>
                                <select name="payment_method" id="edit_payment_method" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Transfer Bank">Transfer Bank</option>
                                    <option value="E-Wallet">E-Wallet</option>
                                    <option value="QRIS">QRIS</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label>Payment Account</label>
                                <select name="payment_account_id" id="edit_payment_account_id" class="form-control">
                                    <option value="">-- Pilih Akun --</option>
                                    @php
                                        // Group accounts by bank name
                                        $groupedBanks = $bankAccounts->groupBy('bank_name');
                                    @endphp

                                    @foreach ($groupedBanks as $bankName => $accounts)
                                        <optgroup label="{{ $bankName }}">
                                            @foreach ($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->account_number }} -
                                                    {{ $acc->account_holder }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-6">
                                <label>No. WA Customer</label>
                                <input type="text" name="customer_phone" id="edit_customer_phone"
                                    class="form-control">
                            </div>
                        </div>

                        <hr>

                        {{-- CUSTOMER --}}
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label>Customer Type</label>
                                <select name="customer_type" id="edit_customer_type" class="form-control" required>
                                    <option value="Non-Reseller">Non-Reseller</option>
                                    <option value="Reseller">Reseller</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Reseller</label>
                                <select name="reseller_id" id="edit_reseller_id" class="form-control" disabled>
                                    <option value="">-- Pilih Reseller --</option>
                                    @foreach ($resellers as $res)
                                        <option value="{{ $res->id }}">{{ $res->namereseller }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label>Catatan</label>
                            <textarea name="notes_transaction" id="edit_notes_transaction" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <!-- Modal Konfirmasi Hapus Transaksi -->
    <div class="modal fade" id="confirmDeleteTransactionModal" tabindex="-1"
        aria-labelledby="confirmDeleteTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteTransactionModalLabel">
                        <i class="fa fa-exclamation-triangle"></i> Konfirmasi Hapus
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus transaksi ini?</p>
                    <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form method="POST" id="formDeleteTransaction">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Approve Transaksi -->
    <div class="modal fade" id="confirmApproveTransactionModal" tabindex="-1"
        aria-labelledby="confirmApproveTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="confirmApproveTransactionModalLabel">
                        <i class="fa fa-check-circle"></i> Konfirmasi Approve Transaksi
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <form method="POST" id="formApproveTransaction" class="w-100">
                    @csrf
                    @method('POST')

                    <div class="modal-body">

                        {{-- STATUS TRANSAKSI --}}
                        <div class="form-group mb-3">
                            <label for="status" class="fw-bold">Status Transaksi</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="PENDING">⏳ Pending</option>
                                <option value="DONE">✅ Selesai (Done)</option>
                                <option value="CANCEL">❌ Batal (Cancel)</option>
                            </select>
                            <small class="form-text text-muted">
                                Pilih status transaksi sesuai hasil verifikasi Role B.
                            </small>
                        </div>

                        {{-- CATATAN PEMBAYARAN --}}
                        <div class="form-group mb-3">
                            <label for="payment_notes" class="fw-bold">Catatan Pembayaran / Keterangan Tambahan</label>
                            <textarea name="payment_notes" id="payment_notes" class="form-control" rows="3"
                                placeholder="Contoh: Transfer dari BCA a.n Andi, atau catatan tambahan lain..."></textarea>
                            <small class="form-text text-muted">
                                Tambahkan catatan terkait pembayaran atau proses approval (opsional).
                            </small>
                        </div>

                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check"></i> Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ============================== --}}
    {{-- MODAL KONFIRMASI TUTUP SHIFT --}}
    {{-- ============================== --}}
    @if ($activeShift)
        <div class="modal fade" id="confirmEndShiftModal" tabindex="-1" aria-labelledby="confirmEndShiftModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('shiftsession.end') }}" id="formCloseShift"
                    data-shift-id="{{ $activeShift->id }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="confirmEndShiftModalLabel">
                                <i class="fa fa-stop-circle"></i> Konfirmasi Tutup Shift
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body ml-10">
                            <p>Apakah Anda yakin ingin menutup shift ini?</p>
                            <ul>
                                <li>Shift yang sudah ditutup tidak dapat dibuka kembali.</li>
                                <li>Semua transaksi yang belum selesai akan dibatalkan.</li>
                                <li>Pastikan semua transaksi telah diselesaikan sebelum menutup shift.</li>
                            </ul>

                            <div class="form-group mt-3">
                                <label for="closing_balance">Close Balance</label>
                                <input type="hidden" name="id" value="{{ $activeShift->id }}" readonly>
                                <input type="number" name="closing_balance" id="closing_balance" class="form-control"
                                    placeholder="Masukkan close balance" required>
                            </div>


                            <!-- menghitung selisih kas jika terdeteksi -->

                            @php
                                // Hitung selisih kasar untuk ditampilkan
                                $totalPaid = \App\Models\Transaction::where('shift_session_id', $activeShift->id)->sum(
                                    'amount_paid',
                                );
                                $saldoSeharusnya = $activeShift->opening_balance + $totalPaid;
                                $selisihKas = $saldoSeharusnya - old('closing_balance', 0);
                            @endphp

                            <hr class="mt-4">

                            <h5 class="text-danger">⚠ Selisih Kas</h5>
                            <p class="text-muted">
                                Sistem akan menghitung selisih ketika shift ditutup. Jika terjadi selisih, silakan tentukan
                                penanggung jawab.
                            </p>

                            <div class="form-group mt-2">
                                <label>Penanggung Jawab Selisih</label>
                                <select name="difference_responsible_id" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    @php
                                        $shiftMembers = \App\Models\ShiftMember::with('user')
                                            ->where('shift_session_id', $activeShift->id)
                                            ->get();
                                          
                                    @endphp

                                    @foreach ($shiftMembers as $member)
                                        @if ($member->user)
                                            <option value="{{ $member->user->idUser }}">
                                                {{ $member->user->nama }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mt-2">
                                <label>Catatan Penyebab Selisih (Opsional)</label>
                                <textarea name="difference_note" class="form-control" rows="2"
                                    placeholder="Contoh: salah penghitungan atau uang terselip..."></textarea>
                            </div>




                            <!-- akhir menghitung selisih kas -->



                            <div class="form-group mt-3">
                                <label for="closing_notes">Catatan Penutupan (opsional)</label>
                                <textarea name="closing_notes" id="closing_notes" class="form-control" rows="3"
                                    placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times"></i> Batal
                            </button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-stop-circle"></i> Tutup Shift
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif



    @if ($activeShifts->count() > 0 && !$joinedShift)
        <!-- Modal Pilih Shift -->
        <div class="modal fade" id="chooseShiftModal" tabindex="-1" aria-labelledby="chooseShiftModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="chooseShiftModalLabel">
                            <i class="fa fa-users"></i> Pilih Shift untuk Bergabung
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">Silakan pilih shift yang sedang aktif untuk bergabung:</p>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode Shift</th>
                                    <th>Dibuka Oleh</th>
                                    <th>Mulai</th>
                                    <th>Saldo Awal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activeShifts as $shift)
                                    <tr>
                                        <td><span class="badge badge-primary">{{ $shift->session_code }}</span></td>
                                        <td>{{ $shift->openedBy->nama ?? 'N/A' }}</td>
                                        <td>{{ $shift->start_time->format('d M Y, H:i') }}</td>
                                        <td>Rp {{ number_format($shift->opening_balance, 0, ',', '.') }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('shiftsession.join') }}">
                                                @csrf
                                                <input type="hidden" name="shift_id" value="{{ $shift->id }}">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa fa-check"></i> Join Shift Ini
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Nanti Saja</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Auto tampilkan modal saat halaman dimuat
            document.addEventListener("DOMContentLoaded", function() {
                const modal = new bootstrap.Modal(document.getElementById('chooseShiftModal'));
                modal.show();
            });
        </script>
    @endif






    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const leaveForms = document.querySelectorAll('form[action*="shiftsession.leave"]');
            leaveForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Keluar dari Shift?',
                        text: "Apakah kamu yakin ingin keluar dari shift aktif saat ini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Keluar',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#d33'
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>





@endsection










@section('scripts')
    <script>
        document.querySelectorAll('form[action*="shiftsession.rejoin"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Gabung Kembali ke Shift?',
                    text: 'Apakah kamu yakin ingin kembali ke shift sebelumnya?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Gabung Kembali',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>



@endsection






@section('scripts')
    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: {!! json_encode(session('success')) !!},
                icon: 'success',
                confirmButtonText: 'Oke'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Gagal!',
                text: {!! json_encode(session('error')) !!},
                icon: 'error',
                confirmButtonText: 'Tutup'
            });
        </script>
    @endif







@endsection
