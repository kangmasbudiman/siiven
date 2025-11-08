<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftCashMovement extends Model
{
    use HasFactory;

    protected $table = 'shift_cash_movements';

    protected $fillable = [
        'shift_session_id',
        'transaction_id',
        'direction',
        'amount',
        'account_id',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function shiftSession()
    {
        return $this->belongsTo(ShiftSession::class, 'shift_session_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }
}
