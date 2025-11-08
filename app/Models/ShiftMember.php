<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftMember extends Model
{
   use HasFactory;

    protected $fillable = [
        'shift_session_id',
        'user_id',
        'joined_at',
        'left_at',
    ];

    public function session()
    {
        return $this->belongsTo(ShiftSession::class, 'shift_session_id');
    }

    public function user()
    {
         return $this->belongsTo(User::class, 'user_id', 'idUser');
    }
      public function useer()
    {
         return $this->belongsTo(User::class, 'opened_by', 'idUser');
    }



}
