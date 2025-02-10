<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Locality extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'administrative_area_id',
        'name',
        'rank',
        'created_by',
        'updated_by',
    ];

    protected $table = 'localities';

    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
    public function state(){
        return $this->belongsTo(AdministrativeArea::class,'administrative_area_id','id');
    }
    public function administrativeArea(){
        return $this->belongsTo(AdministrativeArea::class, 'administrative_area_id');
    }
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

}
