<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Kondisis;
use App\Models\User;

class StockBarang extends Model
{
   protected $table = 'stock_barangs';

    protected $fillable = [
        'barang_id',
        'ruangan_id',
        'jumlah',
        'kondisi_id',
        'user_id',
        'harga',
        'keterangan',
        'tanggalPembelian',
        'nomorInventaris',
        'merk',
        'type',
        'nomorSeri',
        'kondisiPembelian',
        'tanggalPenerimaan'

    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function kondisi()
    {
        return $this->belongsTo(Kondisis::class, 'kondisi_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'idUser');
    }
}
