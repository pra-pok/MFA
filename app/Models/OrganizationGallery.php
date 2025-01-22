<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationGallery extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'gallery_category_id',
        'organization_id',
        'caption',
        'rank',
        'type',
        'media',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $table = 'organization_galleries';

    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function galleryCategory(){
        return $this->belongsTo(GalleryCategory::class,'gallery_category_id','id');
    }

    public function organization(){
        return $this->belongsTo(Organization::class,'organization_id','id');
    }
}
