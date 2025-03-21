<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TargetGroup extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
       'counselor_referrer_id',
        'academic_year_id'
    ];
    protected $table = 'target_groups';

    public function targets()
    {
        return $this->hasMany(Target::class, 'target_group_id');
    }

    public function counselorReferrer()
    {
        return $this->belongsTo(CounselorReferrer::class, 'counselor_referrer_id');
    }
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'id');
    }
}
