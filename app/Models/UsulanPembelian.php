<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsulanPembelian extends Model
{
    use SoftDeletes;

    protected $table = 'usulan_pembelians';

    protected $fillable = [
        'nomor_usulan',
        'tanggal_pengajuan',
        'ruangan_id',
        'nama_penanggung_jawab',
        'keterangan',
        'status',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
    ];

    // Status labels
    public static $statusLabels = [
        'draft'        => ['label' => 'Draft',        'class' => 'secondary'],
        'diajukan'     => ['label' => 'Diajukan',     'class' => 'primary'],
        'diperiksa'    => ['label' => 'Diperiksa',    'class' => 'info'],
        'dikonfirmasi' => ['label' => 'Dikonfirmasi', 'class' => 'info'],
        'diketahui'    => ['label' => 'Diketahui',    'class' => 'warning'],
        'disetujui'    => ['label' => 'Disetujui',    'class' => 'success'],
        'ditolak'      => ['label' => 'Ditolak',      'class' => 'danger'],
    ];

    // Sequential status flow: status saat ini => level approver yang dibutuhkan
    public static $statusFlow = [
        'draft'        => null,
        'diajukan'     => 1, // Kabag/Kabid → diperiksa
        'diperiksa'    => 2, // Direktur → dikonfirmasi
        'dikonfirmasi' => 3, // Ka. Keuangan → diketahui
        'diketahui'    => 4, // Bendahara → disetujui
        'disetujui'    => null,
        'ditolak'      => null,
    ];

    public function getNextStatus(): ?string
    {
        $flow = [
            'diajukan'     => 'diperiksa',
            'diperiksa'    => 'dikonfirmasi',
            'dikonfirmasi' => 'diketahui',
            'diketahui'    => 'disetujui',
        ];
        return $flow[$this->status] ?? null;
    }

    public function getRequiredApprovalLevel(): ?int
    {
        return self::$statusFlow[$this->status] ?? null;
    }

    public static function generateNomor(): string
    {
        $year = date('Y');
        $lastRecord = self::whereYear('created_at', $year)
            ->withTrashed()
            ->orderByDesc('id')
            ->first();

        $sequence = $lastRecord
            ? ((int) substr($lastRecord->nomor_usulan, -3)) + 1
            : 1;

        return 'UPB-' . $year . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function getTotalAttribute(): int
    {
        return $this->details->where('is_ditolak', false)->sum(fn($d) => $d->jumlah * $d->harga_satuan);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status]['label'] ?? $this->status;
    }

    public function getStatusClassAttribute(): string
    {
        return self::$statusLabels[$this->status]['class'] ?? 'secondary';
    }

    // Relationships
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'idUser');
    }

    public function details()
    {
        return $this->hasMany(DetailUsulanPembelian::class, 'usulan_pembelian_id')->orderBy('no_urut');
    }

    public function approvals()
    {
        return $this->hasMany(ApprovalUsulanPembelian::class, 'usulan_pembelian_id')->orderBy('level');
    }

    public function getApprovalByLevel(int $level): ?ApprovalUsulanPembelian
    {
        return $this->approvals->where('level', $level)->where('status', 'approved')->first();
    }
}
