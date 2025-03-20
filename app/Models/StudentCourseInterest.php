<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentCourseInterest extends Model
{
    use SoftDeletes;
    protected $table = 'student_course_interests';

    protected $fillable=[

        'agreement_amount','remarks','current','student_id','course_id'
    ];
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
