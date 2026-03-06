<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;


class Ruangan extends Model
{
   

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'jenis_ruangan',
        'gedung',
        'lantai',
        'penanggung_jawab',
        'jabatan',
        'kontak',
        'user_id',
        'approver_id',
        'is_active'
    ];

    // Relasi ke User (Penanggung Jawab)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'idUser');
    }

    // Approver level-1 yang bertanggung jawab atas ruangan ini (Kabag/Kabid)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id', 'idUser');
    }

    // Relasi ke Barang (nanti)
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}
