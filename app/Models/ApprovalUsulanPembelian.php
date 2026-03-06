<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalUsulanPembelian extends Model
{
    protected $table = 'approval_usulan_pembelians';

    protected $fillable = [
        'usulan_pembelian_id',
        'level',
        'user_id',
        'status',
        'catatan',
        'token',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public static $levelLabels = [
        1 => 'Diperiksa Oleh',     // Kabag/Kabid
        2 => 'Dikonfirmasi Oleh',  // Direktur
        3 => 'Diketahui Oleh',     // Ka. Keuangan
        4 => 'Disetujui Oleh',     // Bendahara
    ];

    public static function generateToken(int $approvalId, string $userId, string $approvedAt): string
    {
        return hash('sha256', $approvalId . $userId . $approvedAt . config('app.key'));
    }

    public function getLevelLabelAttribute(): string
    {
        return self::$levelLabels[$this->level] ?? 'Level ' . $this->level;
    }

    public function usulan()
    {
        return $this->belongsTo(UsulanPembelian::class, 'usulan_pembelian_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'user_id', 'idUser');
    }
}
