<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'slug',
        'rank',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $table = 'levels';

    public function createdBy(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function courses(){
        return $this->hasMany(Course::class,'level_id','id');
    }
}
