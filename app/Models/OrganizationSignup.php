<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationSignup extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password',
        'address',
        'phone',
        'status',
        'created_by',
        'updated_by',
        'comment',
        'tenant_id',
        'organization_role_id',
    ];

    protected $table = 'organization_signup';

    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function tenant(){
        return $this->belongsTo(Tenant::class,'tenant_id','id');
    }

    public function organizationRole(){
        return $this->belongsTo(OrganizationRole::class,'organization_role_id','id');
    }
}
