<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApafaAttendance extends Model
{
   use HasFactory;

    protected $fillable = [
        'apafa_meeting_id',
        'user_id',
        'student_id',
        'status',
        'registered_by',
        'scanned_at',
    ];

    public function meeting()
    {
        return $this->belongsTo(ApafaMeeting::class, 'apafa_meeting_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
