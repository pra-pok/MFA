<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationMember extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'organization_id',
        'organization_group_id',
        'name',
        'rank',
        'designation',
        'photo',
        'bio',
        'status',
        'created_by',
        'updated_by',
    ];
    protected $table = 'organization_members';
    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
    public function organization(){
        return $this->belongsTo(Organization::class,'organization_id','id');
    }
    public function organizationGroup(){
        return $this->belongsTo(OrganizationGroup::class,'organization_group_id','id');
    }
}
