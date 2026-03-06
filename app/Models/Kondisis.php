<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kondisis extends Model
{
    protected $table = 'kondisis';

    protected $fillable = [
        'nama_kondisi',
        'keterangan',
      
    ];

     public function stokBarangs()
    {
        return $this->hasMany(StokBarang::class, 'kondisi_id');
    }
}
