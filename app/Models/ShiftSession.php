<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftSession extends Model
{
    use HasFactory;

    protected $table = 'shift_sessions';

    protected $fillable = [
        'shift_id',
        'session_code',
        'start_time',
        'end_time',
        'opened_by',
        'closed_by',
        'status',
        'opening_balance',
        'ending_balance',
        'note',
        'closing_note',
        'close_balance',
        'difference_responsible_id',
        'difference_note',

    ];

    protected $dates = [
        'start_time',
        'end_time',
        'created_at',
        'updated_at',
    ];

    // ================================
    // 🔗 RELASI
    // ================================

    /**
     * Relasi ke tabel shifts
     * (Setiap sesi shift pasti punya jadwal shift)
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    /**
     * Relasi ke user yang membuka shift
     */
    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    /**
     * Relasi ke user yang menutup shift
     */
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // ================================
    // ⚙️ SCOPES (opsional untuk efisiensi)
    // ================================

    /**
     * Ambil shift aktif (yang sedang berjalan)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Ambil shift berdasarkan user yang sedang login
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('opened_by', $userId);
    }

    // ================================
    // 🧮 FUNGSI TAMBAHAN (helper)
    // ================================

    /**
     * Menghitung durasi shift dalam jam/menit
     */
    public function getDurationAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        $diffInMinutes = $this->end_time->diffInMinutes($this->start_time);
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;

        return sprintf('%02d jam %02d menit', $hours, $minutes);
    }

    /**
     * Menampilkan status dengan label yang rapi
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'open' => 'Shift Aktif',
            'closed' => 'Shift Ditutup',
            default => 'Tidak Diketahui',
        };
    }
    public function members()
{
    return $this->hasMany(ShiftMember::class, 'shift_session_id');
}

public function session()
{
    return $this->belongsTo(ShiftSession::class, 'shift_session_id');
}


}
