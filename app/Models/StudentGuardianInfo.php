<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentGuardianInfo extends Model
{
     protected $table = 'student_guardian_info';
     use SoftDeletes;
     protected $fillable = [
        'name','phone','address','type','student_id','current_guardian'

    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}

