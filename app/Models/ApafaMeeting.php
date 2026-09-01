<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApafaMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
       'title',
        'description',
        'meeting_date',
        'start_time',
        'tolerance_minutes',
        'is_active',
        'status',
    ];

    protected $casts = [
    'is_active' => 'boolean',
    ];

    // Relación: Una reunión tiene muchas asistencias
    public function attendances()
    {
        return $this->hasMany(ApafaAttendance::class, 'apafa_meeting_id');
    }

    //Relacion de pagos
    public function fines()
    {
        return $this->hasMany(ApafaFine::class, 'apafa_meeting_id');
    }
}
