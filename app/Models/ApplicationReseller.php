<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ApplicationReseller extends Model
{
    use HasFactory;

    protected $table = 'application_reseller';

    protected $fillable = [
        'reseller_id',
        'application_id', 
        'special_price',
        'is_active'
    ];

    protected $casts = [
        'special_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Accessor untuk formatted price
    public function getFormattedSpecialPriceAttribute()
    {
        return 'Rp ' . number_format($this->special_price, 0, ',', '.');
    }

    // Tambahkan method ini ke Model Application
        public function resellers()
        {
            return $this->belongsToMany(Reseller::class, 'application_reseller')
                        ->withPivot('special_price', 'is_active')
                        ->withTimestamps();
        }

}
