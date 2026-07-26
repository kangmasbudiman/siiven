<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerawatanBarang extends Model
{
    use SoftDeletes;

    protected $table = 'perawatan_barangs';

    protected $fillable = [
        'stock_barang_id',
        'teknisi_id',
        'tanggal',
        'jam',
        'jenis_perawatan',
        'status',
        'keterangan',
        'foto_sebelum',
        'foto_sesudah',
        'ttd_teknisi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public static $jenisLabels = [
        'pembersihan' => ['label' => 'Pembersihan',  'class' => 'info',    'icon' => 'fa-broom'],
        'preventive'  => ['label' => 'Preventive',   'class' => 'primary', 'icon' => 'fa-shield'],
        'corrective'  => ['label' => 'Corrective',   'class' => 'warning', 'icon' => 'fa-wrench'],
        'kalibrasi'   => ['label' => 'Kalibrasi',    'class' => 'success', 'icon' => 'fa-balance-scale'],
        'lainnya'     => ['label' => 'Lainnya',      'class' => 'secondary','icon' => 'fa-ellipsis-h'],
    ];

    public static $statusLabels = [
        'selesai'    => ['label' => 'Selesai',    'class' => 'success'],
        'pending'    => ['label' => 'Pending',    'class' => 'warning'],
        'bermasalah' => ['label' => 'Bermasalah', 'class' => 'danger'],
    ];

    public function stockBarang()
    {
        return $this->belongsTo(StockBarang::class, 'stock_barang_id');
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id', 'idUser');
    }

    public function getJenisLabelAttribute(): string
    {
        return self::$jenisLabels[$this->jenis_perawatan]['label'] ?? $this->jenis_perawatan;
    }

    public function getJenisClassAttribute(): string
    {
        return self::$jenisLabels[$this->jenis_perawatan]['class'] ?? 'secondary';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status]['label'] ?? $this->status;
    }

    public function getStatusClassAttribute(): string
    {
        return self::$statusLabels[$this->status]['class'] ?? 'secondary';
    }

    public function scopeLatestFirst($q)
    {
        return $q->orderByDesc('tanggal')->orderByDesc('jam');
    }

    public function scopeByStock($q, $stockId)
    {
        return $q->where('stock_barang_id', $stockId);
    }
}
