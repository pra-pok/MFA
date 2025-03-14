<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Student;
use App\Models\CounselorReferrer;

class StudentCounselorReffer extends Model
{
    use SoftDeletes;

    protected $table = "student_counselor_referrers";
    protected $guard = [];


    public function student()
    {
        return $this->belongsTo(Student::class);
    }


}
