<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdministrativeArea extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'rank',
        'parent_id',
        'status',
        'created_by',
        'updated_by',
        'country_id'
    ];

    protected $table = 'administrative_area';

    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function parent(){
        return $this->belongsTo(AdministrativeArea::class,'parent_id','id');
    }
    public function children(){
        return $this->hasMany(AdministrativeArea::class,'parent_id','id');
    }
    public function country(){
        return $this->belongsTo(Country::class,'country_id','id');
    }
    public function localities()
    {
        return $this->hasMany(Locality::class);
    }
}
