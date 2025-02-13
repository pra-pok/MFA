<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseCatalog extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id',
        'catalog_id',
    ];

    protected $table = 'course_catalogs';

    public function catalog(){
        return $this->belongsTo(Catalog::class,'catalog_id','id');
    }

    public function course(){
        return $this->belongsTo(Course::class,'course_id','id');
    }
}
