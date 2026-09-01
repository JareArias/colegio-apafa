<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApafaFine extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'apafa_meeting_id',
        'amount',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function meeting()
    {
        return $this->belongsTo(ApafaMeeting::class, 'apafa_meeting_id');
    }
}
