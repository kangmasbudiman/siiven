<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_session_id',
        'application_id',
        'reseller_id',
        'type',
        'subtype',
        'reference_id',
        'quantity',
        'rate',
        'amount',
        'paid_amount',
        'debt_amount',
        'account_id',
        'notes_transaction',
        'status',
        'created_by',
        'executed_by',
        'executed_at',
        'customer',
        'customer_phone',
        'balance_after',
        'customer_type'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'debt_amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'executed_at' => 'datetime',
    ];

    // Relationships
    public function shiftSession()
    {
        return $this->belongsTo(ShiftSession::class);
    }

    public function application()
    {
        return $this->belongsTo(Aplikasi::class, 'application_id');
    }

    public function reseller()
    {
        return $this->belongsTo(Reseller::class, 'reseller_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function executor()
    {
        return $this->belongsTo(\App\Models\User::class, 'executed_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopePendingExec($query)
    {
        return $query->where('status', 'PENDING_EXEC');
    }

    // Helpers
    /**
     * total kalkulasi dari item jika amount kosong
     */
    public function getTotalAttribute()
    {
        // jika amount sudah diisi gunakan itu
        if ($this->amount !== null && $this->amount > 0) {
            return (float) $this->amount;
        }

        // sum dari items
        return (float) $this->items()->selectRaw('COALESCE(SUM(price * qty),0) as total')->pluck('total')->first() ?? 0.0;
    }
}
