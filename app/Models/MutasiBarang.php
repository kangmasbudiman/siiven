<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    protected $table = 'mutasi_barangs';

    protected $fillable = [
        'barang_id',
        'ruangan_asal_id',
        'ruangan_tujuan_id',
        'jumlah',
        'user_id',
        'keterangan'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function ruanganAsal()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_asal_id');
    }

    public function ruanganTujuan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_tujuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'idUser');
    }
}
