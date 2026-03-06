<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategoris extends Model
{
    protected $table = 'kategoris';

    protected $fillable = [
        'nama_kategori',
        'keterangan',
        'is_active'
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }
}
