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
        $users = User::all();
        return view('admin.components.user.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize(Permissions::USER_CREATE);
        $data['roles'] = Role::pluck('name', 'id');
        return view('admin.components.user.create' , compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize(Permissions::USER_CREATE);
      // dd($request->all());
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'team_id' => getPermissionsTeamId(),
            'role_id' => $request->role_id,
            'status' => $request->status,
            'created_by' => $request->user()->id,
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
        $data['roles'] = Role::pluck('name', 'id');
        return view('admin.components.user.edit', ['user' => $user , 'data' => $data]);
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
        $user->username = $request->username;
        $user->email = $validated['email'];
        $user->role_id = $request->role_id;
        $user->status = $request->status;
        $user->updated_by = $request->user()->id;
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

        if ($user->id == $request->user()->id) {
            return redirect()->route('user.index')->withError('You cannot assign role to yourself');
        }

        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('admin.components.user.assign-roles', ['user' => $user, 'roles' => $roles]);
    }

    public function saveAssignRoles(Request $request, User $user)
    {
        $this->authorize(Permissions::USER_ASSIGN_ROLES);

        if ($user->team_id != getPermissionsTeamId()) {
            abort(403, 'Unauthorized');
        }


        if ($user->id == $request->user()->id) {
            return redirect()->route('user.index')->withError('You cannot assign role to yourself');
        }

        $user->syncRoles($request->roles);
        return redirect()
            ->route('user.index')
            ->withSuccess('Roles assigned successfully');
    }
    public function reset(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);
        try {
            $user = User::findOrFail($request->id);
            $user->password = Hash::make($request->password);
            $user->save();
            if ($user) {
                $request->session()->flash('alert-success', $this->panel . ' Password Change successfully!');
            } else {
                $request->session()->flash('alert-danger', $this->panel . ' creation failed!');
            }
        } catch (\Exception $exception) {
            $request->session()->flash('alert-danger', 'Database Error: ' . $exception->getMessage());
        }
        return redirect()->route( 'user.index');
    }
    public function block(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'comment' => $request->status == 0 ? 'nullable' : 'nullable',
        ]);
        try {
            $user = User::findOrFail($request->id);
            $newStatus = $user->status == 1 ? 0 : 1;
            if ($newStatus == 0 && !$request->comment) {
                return response()->json(['success' => false, 'message' => 'Comment is required to block.'], 422);
            }
            $user->status = $newStatus;
            $user->comment = $request->comment ?? $user->comment;
            $user->save();
            return response()->json([
                'success' => true,
                'status' => $user->status,
                'message' => $newStatus == 1 ? 'Organization Unblocked successfully!' : 'Organization Blocked successfully!',
            ]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => 'Database Error: ' . $exception->getMessage()], 500);
        }
    }
    public function clearComment(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user) {
            $user->comment = null;
            $user->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    public function getDataMessage(Request $request)
    {
        $user = User::find($request->id);
        if ($user) {
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $user->name,
                    'comment' => $user->comment,
                    'status' => $user->status
                ]
            ]);
        }
        return response()->json(['success' => false, 'message' => 'Organization not found'], 404);
    }
}
