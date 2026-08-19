<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
   public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student');
    }
}
