<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserShift extends Model
{
    use HasFactory;

    protected $table = 'user_shifts';
    
    protected $fillable = [
        'user_id',
        'shift_id',
        'is_active',
        'assigned_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'idUser');
    }

    // Relasi ke Shift
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    

    // Relasi ke User yang assign
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'idUser');
    }







} 