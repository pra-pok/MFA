<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationRole extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'tenant_id',
        'status',
    ];

    protected $table = 'organization_roles';

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function organizationSignups()
    {
        return $this->hasMany(OrganizationSignup::class, 'organization_role_id');
    }


}
