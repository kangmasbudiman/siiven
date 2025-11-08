<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_datetime', 
        'shift_session_id', 
        'admin_id', 
        'app_id',
        'coin_type', 
        'rate', 
        'amount_due', 
        'coin_qty', 
        'payment_type',
        'amount_paid', 
        'outstanding_amount', 
        'payment_method',
        'payment_account_id', 
        'customer_type', 
        'reseller_id',
        'customer_phone',
        'notes_transaction', 
        'status', 
        'processed_by',
        'processed_datetime', 
        'process_notes',
    ];

    protected $casts = [
        'transaction_datetime' => 'datetime',
        'processed_datetime' => 'datetime',
        'amount_due' => 'decimal:2',
        'rate' => 'decimal:2',
        'coin_qty' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
    ];

    // Auto-relasi
    public function application() { return $this->belongsTo(Aplikasi::class, 'app_id'); }
    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }
    public function shift() { return $this->belongsTo(ShiftSession::class); }
    public function paymentAccount() { return $this->belongsTo(Bank::class, 'payment_account_id'); }
    public function reseller() { return $this->belongsTo(Reseller::class); }
    public function executor() { return $this->belongsTo(User::class, 'processed_by'); }

    // Auto perhitungan
    public static function boot()
    {
        parent::boot();

        static::creating(function ($trx) {
            $trx->transaction_datetime = now(); // otomatis timestamp server
            $trx->coin_qty = $trx->rate ? $trx->amount_due / $trx->rate : 0;
            $trx->outstanding_amount = $trx->amount_due - $trx->amount_paid;
        });

        static::updating(function ($trx) {
            $trx->coin_qty = $trx->rate ? $trx->amount_due / $trx->rate : 0;
            $trx->outstanding_amount = $trx->amount_due - $trx->amount_paid;
        });
    }
}
