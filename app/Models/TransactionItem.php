<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $table = 'transaction_items';

    protected $fillable = [
        'transaction_id',
        'item_name',
        'price',
        'qty',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'qty' => 'integer',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->qty;
    }
}
