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
        'administrative_area_id',
        'name',
        'slug',
        'logo',
        'banner_image',
        'address',
        'phone',
        'email',
        'website',
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
        'updated_by'
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
}
