<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'dni',
        'name',
        'grade',
        'section',
        'level',
    ];

   public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
