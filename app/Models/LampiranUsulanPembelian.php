<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LampiranUsulanPembelian extends Model
{
    protected $table = 'lampiran_usulan_pembelians';

    protected $fillable = [
        'usulan_pembelian_id',
        'detail_usulan_pembelian_id',
        'nama_file',
        'path',
        'mime_type',
        'ukuran',
    ];

    public function usulan()
    {
        return $this->belongsTo(UsulanPembelian::class, 'usulan_pembelian_id');
    }

    public function detail()
    {
        return $this->belongsTo(DetailUsulanPembelian::class, 'detail_usulan_pembelian_id');
    }

    /** Lampiran umum (per usulan, bukan per item) */
    public function scopeUmum($query)
    {
        return $query->whereNull('detail_usulan_pembelian_id');
    }

    /** Lampiran per item */
    public function scopePerItem($query)
    {
        return $query->whereNotNull('detail_usulan_pembelian_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getFullPathAttribute(): string
    {
        return Storage::path($this->path);
    }
}
