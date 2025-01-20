<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'rank',
        'iso_code',
        'currency',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'icon',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $table = 'countries';

    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
    public function universities(){
        return $this->hasMany(University::class,'country_id','id');
    }
}
