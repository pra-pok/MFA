<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    protected $table = 'roles';

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    public function syncPermissions(array $permissions)
    {
        $this->permissions()->sync($permissions);
    }
//    public function permissions(): BelongsToMany
//    {
//        return $this->belongsToMany(
//            config('permission.models.permission'),
//            config('permission.table_names.role_has_permissions'),
//            app(PermissionRegistrar::class)->pivotRole,
//            app(PermissionRegistrar::class)->pivotPermission
//        );
//    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name'] ?? config('auth.defaults.guard')),
            'model',
            config('permission.table_names.model_has_roles'),
            app(PermissionRegistrar::class)->pivotRole,
            config('permission.column_names.model_morph_key')
        );
    }

}
