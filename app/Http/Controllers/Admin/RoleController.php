<?php

namespace App\Http\Controllers\Admin;

use App\Enum\Permissions;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(Permissions::ROLE_READ);

        $roles = Role::all();
        return view('admin.components.role.index', ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize(Permissions::ROLE_CREATE);

        return view('admin.components.role.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize(Permissions::ROLE_CREATE);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')],
        ]);

        Role::create(['name' => $validated['name']]);
        return redirect()
            ->route('role.index')
            ->withSuccess('Role created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $this->authorize(Permissions::ROLE_UPDATE);

        if ($role->team_id != getPermissionsTeamId()) {
            abort(403, 'Unauthorized');
        }

        if ($role->name == 'Super Admin') {
            return redirect()->route('role.index')->withError('Super Admin cannot be edited');
        }

        return view('admin.components.role.edit', ['role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize(Permissions::ROLE_UPDATE);

        if ($role->team_id != getPermissionsTeamId()) {
            abort(403, 'Unauthorized');
        }

        if ($role->name == 'Super Admin') {
            return redirect()->route('role.index')->withError('Super Admin cannot be edited');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')],
        ]);

        $role->name = $validated['name'];
        $role->save();
        return redirect()
            ->route('role.index')
            ->withSuccess('Role updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Role $role)
    {
        $this->authorize(Permissions::ROLE_DELETE);

        $role->delete();
        return redirect()
            ->route('role.index')
            ->withSuccess('Role deleted successfully');
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $this->authorize(Permissions::ROLE_ASSIGN_PERMISSIONS);

        if ($role->team_id != getPermissionsTeamId()) {
            abort(403, 'Unauthorized');
        }

        if ($role->name == 'Super Admin') {
            return redirect()->route('role.index')->withError('Super Admin cannot be assigned permissions');
        }

        // allow all permissions in assign page for superadmin
        if ($request->user()->team_id == 1) {
            $permissions = Permission::all();
        } else {
            $teamSuperAdminRole = Role::first();
            $permissions = $teamSuperAdminRole->permissions;
        }

        return view('admin.components.role.assign-permissions', ['role' => $role, 'permissions' => $permissions]);
    }

    public function saveAssignPermissions(Request $request, Role $role)
    {
        $this->authorize(Permissions::ROLE_ASSIGN_PERMISSIONS);

        if ($role->team_id != getPermissionsTeamId()) {
            abort(403, 'Unauthorized');
        }

        if ($role->name == 'Super Admin') {
            return redirect()->route('role.index')->withError('Super Admin cannot be assigned permissions');
        }
        // Log the permissions to debug
        \Log::info('Permissions:', $request->permissions);

        // Sync permissions
        $role->syncPermissions($request->permissions);

        return redirect()
            ->route('role.index')
            ->withSuccess('Permissions assigned successfully');
    }
}
