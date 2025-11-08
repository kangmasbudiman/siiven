<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Reseller extends Model
{
     use HasFactory;

    protected $fillable = [
        'namereseller',
        'code',
        'phone', 
        'email',
        'address',
        'initial_balance',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'is_active' => 'boolean'
    ];

 

    public function transactions()
    {
        return $this->hasMany(Transaction::class); // Akan dibuat nanti
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHasDebt($query)
    {
        return $query->where('initial_balance', '>', 0);
    }

    // Helper Methods
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->initial_balance, 0, ',', '.');
    }

 

    public function canDelete()
    {
        return $this->transactions()->doesntExist();
    }

}
