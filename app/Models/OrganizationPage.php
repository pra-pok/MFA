<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationPage extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'organization_id',
        'page_category_id',
        //'title',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $table = 'organization_pages';
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
    public function page(){
        return $this->belongsTo(PageCategory::class,'page_category_id','id');
    }

}
