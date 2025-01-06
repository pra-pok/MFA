<?php

namespace App\Http\Controllers\Admin;

use App\Enum\Permissions;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class TeamController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize(Permissions::TEAM_READ);

        $teams = Team::all();
        return view('pages.team.index', ['teams' => $teams]);
    }

    public function create(Request $request)
    {
        $this->authorize(Permissions::TEAM_CREATE);

        return view('pages.team.create');
    }

    public function store(Request $request)
    {
        $this->authorize(Permissions::TEAM_CREATE);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('teams')],
            'admin_email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'admin_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $team = new Team();
        $team->fill($validated);
        $team->save();

        // create user
        $user = User::create([
            'name' => $team->name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'team_id' => $team->id
        ]);

        $sessionTeamId = getPermissionsTeamId();
        setPermissionsTeamId($team);

        $role = Role::create(['name' => 'Super Admin', 'team_id' => $team->id]);
        $user->assignRole($role);

        setPermissionsTeamId($sessionTeamId);

        return redirect()
            ->route('team.index')
            ->withSuccess('Team created successfully');
    }

    public function edit(Request $request, Team $team)
    {
        $this->authorize(Permissions::TEAM_UPDATE);

        return view('pages.team.edit', ['team' => $team]);
    }

    public function update(Request $request, Team $team)
    {
        $this->authorize(Permissions::TEAM_UPDATE);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('teams')->ignore($team->id)],
        ]);

        $team->name = $validated['name'];
        $team->save();
        return redirect()
            ->route('team.index')
            ->withSuccess('Team updated successfully');
    }

    public function destroy(Request $request, Team $team)
    {
        $this->authorize(Permissions::TEAM_DELETE);

        if ($team->id == 1) {
            return redirect()->route('team.index')->withError('You cannot delete this team');
        }

        if ($request->user()->team_id == $team->id) {
            return redirect()->route('team.index')->withError('You cannot delete your own team');
        }

        if ($team->users()->count() > 0) {
            return redirect()->route('team.index')->withError('Team has users');
        }

        $team->delete();
        return redirect()->route('team.index')->withSuccess('Team deleted successfully');
    }

    public function assignPermission(Request $request, Team $team)
    {
        $this->authorize(Permissions::TEAM_ASSIGN_PERMISSIONS);

        if ($team->id == 1) {
            return redirect()->route('team.index')->withError('Permissions cannot be assigned to this team');
        }

        $permissions = Permissions::all();

        $teamSuperAdminRole = Role::where(['team_id' => $team->id, 'name' => 'Super Admin'])->first();

        return view('pages.team.assign-permissions', [
            'team' => $team,
            'teamSuperAdminRole' => $teamSuperAdminRole,
            'permissions' => $permissions
        ]);
    }

    public function saveAssignPermission(Request $request, Team $team)
    {
        $this->authorize(Permissions::TEAM_ASSIGN_PERMISSIONS);

        if ($team->id == 1) {
            return redirect()->route('team.index')->withError('Permissions cannot be assigned to this team');
        }

        $teamSuperAdminRole = Role::where(['team_id' => $team->id, 'name' => 'Super Admin'])->first();

        setPermissionsTeamId($team->id);
        $teamSuperAdminRole->syncPermissions($request->permissions);
        setPermissionsTeamId($request->user()->team_id);

        return redirect()->route('team.index')->withSuccess('Permissions assigned successfully');
    }
}
