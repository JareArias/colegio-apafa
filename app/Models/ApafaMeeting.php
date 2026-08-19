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
        'is_active',
    ];

    // Relación: Una reunión tiene muchas asistencias
    public function attendances()
    {
        return $this->hasMany(ApafaAttendance::class, 'apafa_meeting_id');
    }
}
