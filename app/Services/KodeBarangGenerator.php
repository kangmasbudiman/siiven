<?php

namespace App\Services;

use App\Models\Barang;

class KodeBarangGenerator
{
    public static function generate(): string
    {
        $prefix = 'RSRPJ';

        // ambil kode terakhir
        $lastBarang = Barang::where('kode_barang', 'like', $prefix.'-%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastBarang) {
            return $prefix . '-000001';
        }

        // ambil angka terakhir
        $lastNumber = (int) substr($lastBarang->kode_barang, -6);
        $nextNumber = $lastNumber + 1;

        return $prefix . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
