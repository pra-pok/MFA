<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Catalog extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'rank',
        'data',
        'type',
        'status',
        'created_by',
        'updated_by',
    ];
    protected $table = 'catalogs';
    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function organizationCatalog(){
        return $this->hasMany(OrganizationCatalog::class,'catalog_id','id');
    }

    public function organization(){
        return $this->belongsToMany(Organization::class,'organization_catalogs','catalog_id','organization_id');
    }

    public function catalogCategory(){
        return $this->hasMany(Catalog::class,'catalog_id','id');
    }

}
