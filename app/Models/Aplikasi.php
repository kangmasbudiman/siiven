<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aplikasi extends Model
{
   use HasFactory;

    protected $fillable = [
        'nameaplication',
        'code', 
        'normal_price',
        'description',
        'coin_type',
        'rate',
        'is_active'
    ];

    protected $casts = [
        'normal_price' => 'decimal:2',
        'rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function resellers()
    {
        return $this->hasMany(Reseller::class);
    }

    public function shiftSessions()
    {
        return $this->hasMany(ShiftSession::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->normal_price, 0, ',', '.');
    }

    public function canDelete()
    {
        return !$this->resellers()->exists() && !$this->shiftSessions()->exists();
    }
}
