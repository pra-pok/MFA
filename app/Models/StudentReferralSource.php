<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentReferralSource extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'referral_source_id',
    ];
    protected $table = 'student_referral_sources';

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function referralSource()
    {
        return $this->belongsTo(ReferralSource::class);
    }
}
