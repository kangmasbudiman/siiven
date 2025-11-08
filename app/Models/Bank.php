<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
  use HasFactory;

    protected $fillable = [
        'bank_name',
        'account_number', 
        'account_name',
        'branch',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

   // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByBankName($query, $bankName)
    {
        return $query->where('bank_name', $bankName);
    }

    // Helper Methods
    public function getFormattedAccountNumberAttribute()
    {
        // Format: 1234-5678-9012
        return implode('-', str_split($this->account_number, 4));
    }

    public function getFullBankInfoAttribute()
    {
        return "{$this->bank_name} - {$this->account_name} ({$this->getFormattedAccountNumberAttribute()})";
    }

    public function canDelete()
    {
        // Cek apakah bank sudah digunakan di transaksi/shift sessions
        // Sesuaikan dengan relationships yang akan dibuat
        return true;
    }
}
