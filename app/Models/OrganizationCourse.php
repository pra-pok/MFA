<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationCourse extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'organization_id',
        'course_id',
        'start_fee',
        'end_fee',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $table = 'organization_courses';
    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
    public function organization(){
        return $this->belongsTo(Organization::class,'organization_id','id');
    }
    public function course(){
        return $this->belongsTo(Course::class,'course_id','id');
    }



}
