<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationCatalog extends Model
{
    use HasFactory;
    protected $fillable = [
        'organization_id',
        'catalog_id',
    ];

    protected $table = 'organization_catalogs';


    public function organization(){
        return $this->belongsTo(Organization::class,'organization_id','id');
    }

    public function catalog(){
        return $this->belongsTo(Catalog::class,'catalog_id','id');
    }
}
