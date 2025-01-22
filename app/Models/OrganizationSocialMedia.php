<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationSocialMedia extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'organization_id',
        'name',
        'url',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $table = 'organization_social_medias';
    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
}
