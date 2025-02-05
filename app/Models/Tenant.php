<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'organization_signup_id',
        'status',
    ];

    protected $table = 'tenants';

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_signup_id');
    }

    public function tenants()
    {
        return $this->hasMany(OrganizationSignup::class, 'tenant_id');

    }

}
