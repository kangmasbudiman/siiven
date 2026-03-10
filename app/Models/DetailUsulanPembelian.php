<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailUsulanPembelian extends Model
{
    protected $table = 'detail_usulan_pembelians';

    protected $fillable = [
        'usulan_pembelian_id',
        'no_urut',
        'keterangan',
        'jumlah',
        'harga_satuan',
        'is_ditolak',
        'alasan_tolak',
    ];

    protected $casts = [
        'is_ditolak' => 'boolean',
    ];

    public function getSubtotalAttribute(): int
    {
        return $this->is_ditolak ? 0 : $this->jumlah * $this->harga_satuan;
    }

    public function usulan()
    {
        return $this->belongsTo(UsulanPembelian::class, 'usulan_pembelian_id');
    }

    public function lampirans()
    {
        return $this->hasMany(LampiranUsulanPembelian::class, 'detail_usulan_pembelian_id')->orderBy('id');
    }
}
