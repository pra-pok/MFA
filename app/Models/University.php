<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class University extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'short_title',
        'slug',
        'rank',
        'types',
        'description',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'logo',
        'status',
        'created_by',
        'updated_by',
        'country_id'
    ];

    protected $table = 'universities';

    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
    public function country(){
        return $this->belongsTo(Country::class,'country_id','id');
    }
    public function organizationCourses()
    {
        return $this->hasMany(OrganizationCourse::class, 'university_id', 'id');
    }

    public function universityCatalogs()
    {
        return $this->hasMany(UniversityCatalog::class, 'university_id', 'id');
    }
    public function catalogs()
    {
        return $this->belongsToMany(Catalog::class, 'university_catalogs', 'university_id', 'catalog_id');
    }
}
