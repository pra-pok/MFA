<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StudentCounselorReffer;

class CounselorReferrer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'counselor_referrers';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'role',
        'created_by',
        'updated_by',
    ];


    public function createds(){
        return $this->belongsTo(OrganizationSignup::class,'created_by','id');
    }
    public function updatedBy(){
        return $this->belongsTo(OrganizationSignup::class,'updated_by','id');
    }

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'student_counselor_referrers',
            'counselor_referred_id',
            'student_id'
        );
    }

    public function targetGroups()
    {
        return $this->hasMany(TargetGroup::class, 'counselor_referrer_id'); // Adjust FK if different
    }

}
