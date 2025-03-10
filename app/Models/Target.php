<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Target extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'counselor_referrer_id',
        'academic_year_id',
        'min_target',
        'max_target',
        'amount_percentage',
        'type',
    ];
    protected $table = 'targets';
    public function counselorReferrer()
    {
        return $this->belongsTo(CounselorReferrer::class, 'counselor_referrer_id');
    }
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}
