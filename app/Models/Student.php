<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StudentCounselorReffer;

class Student extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
        'permanent_address',
        'temporary_address',
        'permanent_locality_id',
        'temporary_locality_id',
        'referral_source_id',
        'counselor_referred_id',
        'tenant_id'
    ];

    protected $table = 'students';

    public function permanentLocality()
    {
        return $this->belongsTo(Locality::class, 'permanent_locality_id');
    }

    public function temporaryLocality()
    {
        return $this->belongsTo(Locality::class, 'temporary_locality_id');
    }

    public function referralSource()
    {
        return $this->belongsTo(ReferralSource::class, 'referral_source_id');
    }

    public function counselorReferred()
    {
        return $this->belongsTo(CounselorReferrer::class, 'counselor_referred_id');
    }

    public function counselors()
    {
        return $this->belongsToMany(
            CounselorReferrer::class,
            'student_counselor_referrers',
            'student_id',
            'counselor_referred_id'
        );
    }

    public function guardianInfo()
    {
        return $this->hasMany(StudentGuardianInfo::class);
    }

    public function educationHistory()
    {
        return $this->hasMany(StudentEducationHistory::class);
    }

    public function courseInterests()
    {
        return $this->hasMany(StudentCourseInterest::class);
    }

    public function studentCounselorReffer()
    {
        return $this->hasMany(StudentCounselorReffer::class);
    }

    public function studentReferralSource()
    {
        return $this->hasMany(StudentReferralSource::class);
    }

    public function studentDocuments()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class, 'student_id');
    }

    public function tenants()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
