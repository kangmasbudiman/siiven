<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'amount',
        'type',
        'note',
        'user_id'
    ];


    public function aplikasi()
    {
        return $this->belongsTo(Aplikasi::class, 'app_id');
    }
    

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
