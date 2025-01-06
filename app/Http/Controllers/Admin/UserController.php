<?php

namespace App\Http\Controllers\Admin;

use App\Enum\Permissions;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize(Permissions::USER_READ);

        $users = User::where('team_id', getPermissionsTeamId())->get();
        return view('pages.user.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize(Permissions::USER_CREATE);

        return view('pages.user.create');
    }

    /**
     * Store a newly created resource in storage.   
     */
    public function store(Request $request)
    {
        $this->authorize(Permissions::USER_CREATE);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'team_id' => getPermissionsTeamId()
        ]);

        return redirect()
            ->route('user.index')
            ->withSuccess('User created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorize(Permissions::USER_UPDATE);

        if ($user->team_id != getPermissionsTeamId()) {
            abort(403, 'Unauthorized');
        }

        return view('pages.user.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize(Permissions::USER_UPDATE);

        if ($user->team_id != getPermissionsTeamId()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();
        return redirect()
            ->route('user.index')
            ->withSuccess('User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        $this->authorize(Permissions::USER_DELETE);

        if ($user->team_id != getPermissionsTeamId()) {
            abort(403, 'Unauthorized');
        }

        if ($user->hasRole('Super Admin')) {
            return redirect()->route('user.index')->withError('Super Admin cannot be deleted');
        }

        if ($user->id == $request->user()->id) {
            return redirect()->route('user.index')->withError('You cannot delete yourself');
        }

        $user->delete();
        return redirect()
            ->route('user.index')
            ->withSuccess('User deleted successfully');
    }

    public function assignRoles(Request $request, User $user)
    {
        $this->authorize(Permissions::USER_ASSIGN_ROLES);

        if ($user->team_id != getPermissionsTeamId()) {
            abort(403, 'Unauthorized');
        }

        if ($user->hasRole('Super Admin')) {
            return redirect()->route('user.index')->withError('Super Admin cannot be assigned roles');
        }

        if ($user->id == $request->user()->id) {
            return redirect()->route('user.index')->withError('You cannot assign role to yourself');
        }

        $roles = Role::where('team_id', getPermissionsTeamId())->where('name', '!=', 'Super Admin')->get();
        return view('pages.user.assign-roles', ['user' => $user, 'roles' => $roles]);
    }

    public function saveAssignRoles(Request $request, User $user)
    {
        $this->authorize(Permissions::USER_ASSIGN_ROLES);

        if ($user->team_id != getPermissionsTeamId()) {
            abort(403, 'Unauthorized');
        }

        if ($user->hasRole('Super Admin')) {
            return redirect()->route('user.index')->withError('Super Admin cannot be assigned roles');
        }

        if ($user->id == $request->user()->id) {
            return redirect()->route('user.index')->withError('You cannot assign role to yourself');
        }

        $user->syncRoles($request->roles);
        return redirect()
            ->route('user.index')
            ->withSuccess('Roles assigned successfully');
    }
}
