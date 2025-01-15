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
        'updated_by'
    ];

    protected $table = 'courses';

    public function createdBy(){
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
}
