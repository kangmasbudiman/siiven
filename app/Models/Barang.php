<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
   protected $table = 'barangs';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori_id',
        'jenis_barang',
        'merk',
        'satuan',
        'spesifikasi',
        'is_active'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategoris::class, 'kategori_id');
    }

    public function stokBarangs()
    {
        return $this->hasMany(StokBarang::class, 'barang_id');
    }

    public function mutasiBarangs()
    {
        return $this->hasMany(MutasiBarang::class, 'barang_id');
    }
}
