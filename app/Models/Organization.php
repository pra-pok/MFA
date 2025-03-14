<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'short_name',
        'slug',
        'logo',
        'banner_image',
        'address',
        'phone',
        'email',
        'website',
        'country_id',
        'description',
        'type',
        'search_keywords',
        'established_year',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'total_view',
        'status',
        'created_by',
        'updated_by',
        'locality_id',
        'google_map',
    ];

    protected $table = 'organizations';

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

    public function administrativeArea(){
        return $this->belongsTo(AdministrativeArea::class,'administrative_area_id','id');
    }

    public function country(){
        return $this->belongsTo(Country::class,'country_id','id');
    }

    public function locality(){
        return $this->belongsTo(Locality::class,'locality_id','id');
    }

    public function socialMediaLinks()
    {
        return $this->hasMany(OrganizationSocialMedia::class);
    }

    public function organizationsocialMedia()
    {
        return $this->hasMany(OrganizationSocialMedia::class);
    }

    public function galleryCategory(){
        return $this->hasMany(GalleryCategory::class,'organization_id','id');
    }

    public function organizationGalleries(){
        return $this->hasMany(OrganizationGallery::class,'organization_id','id');
    }

    public function organizationCourses(){
        return $this->hasMany(OrganizationCourse::class,'organization_id','id');
    }

    public function organizationPages(){
        return $this->hasMany(OrganizationPage::class,'organization_id','id');
    }

    public function organizationfacilities(){
        return $this->hasMany(OrganizationFacilities::class,'organization_id','id');
    }
    public function organizationCatalog(){
        return $this->hasMany(OrganizationCatalog::class,'organization_id','id');
    }
    public function catalog(){
        return $this->belongsToMany(Catalog::class,'organization_catalogs','organization_id','catalog_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'organization_id', 'id');
    }
    public function organizationReviews()
    {
        return $this->hasMany(Review::class, 'organization_id', 'id');
    }

    public function organizationNewsEvents()
    {
        return $this->belongsToMany(NewEvent::class, 'organization_new_events', 'organization_id', 'new_event_id');
    }

    public function organizationMembers ()
    {
        return $this->hasMany(OrganizationMember::class,'organization_id','id');
    }
}
