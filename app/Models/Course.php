<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'short_title',
        'slug',
        'rank',
        'stream_id',
        'level_id',
        'description',
        'eligibility',
        'job_prospects',
        'syllabus',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'status',
        'created_by',
        'updated_by',
        'duration',
        'min_range_fee',
        'max_range_fee',
    ];

    protected $table = 'courses';

    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function stream(){
        return $this->belongsTo(Stream::class,'stream_id','id');
    }

    public function level(){
        return $this->belongsTo(Level::class,'level_id','id');
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_courses', 'course_id', 'organization_id');
    }
    public function catalogs()
    {
        return $this->belongsToMany(Catalog::class, 'course_catalogs', 'course_id', 'catalog_id');
    }
    public function courseCatalogs()
    {
        return $this->hasMany(CourseCatalog::class, 'course_id', 'id');
    }
}
