<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facilities extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'rank',
        'icon',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $table = 'facilities';

    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function organizationfacilities(){
        return $this->hasMany(OrganizationFacilities::class,'faculty_id','id');
    }

}
