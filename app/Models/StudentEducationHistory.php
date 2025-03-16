<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentEducationHistory extends Model
{
     use SoftDeletes;
     protected $table = 'student_education_history';
     protected $fillable = [
        'name','address','marks_received','note','course_studied','student_id',

    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
