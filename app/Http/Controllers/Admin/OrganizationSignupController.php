<?php

namespace App\Http\Controllers\Admin;

use App\Dtos\ResponseDTO;
use App\Http\Requests\OrganizationSignupRequest;
use App\Models\OrganizationRole;
use App\Models\OrganizationSignup;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Utils;
use Illuminate\Support\Facades\Storage;

class OrganizationSignupController extends DM_BaseController
{
    protected $panel = 'College / School Account';
    protected $base_route = 'organization-signup';
    protected $view_path = 'admin.components.organization-signup';
    protected $model;
    protected $table;
    protected $folder = 'organization-signup';


    public function __construct(Request $request, OrganizationSignup $organization_signup)
    {
        $this->model = $organization_signup;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                // Check if tenant_id is present
                $tenantId = $request->get('tenant_id');
                $data = $this->model->with([
                    'createds' => function ($query) {
                        $query->select('id', 'username');
                    },
                    'updatedBy' => function ($query) {
                        $query->select('id', 'username');
                    }
                ])
                    ->leftJoin("tenants", "tenants.id", "=", "organization_signup.tenant_id")
                    ->when($tenantId, function ($query) use ($tenantId) {
                        return $query->where('organization_signup.tenant_id', $tenantId);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

                return Utils\ResponseUtil::wrapResponse(new ResponseDTO($data, 'Data retrieved successfully.', 'success'));
            }
        } catch (\Exception $exception) {
            return Utils\ResponseUtil::wrapResponse(new ResponseDTO($data, 'Error retrieved Error.', 'error'));
        }
        return view(parent::loadView($this->view_path . '.index'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     *
     */
    public function create(Request $request)
    {
        return view(parent::loadView($this->view_path . '.create'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
//    public function store(OrganizationSignupRequest $request)
//    {
//        try {
//
//            $defaultRoleId = 'Super admin';
//            if($defaultRoleId) {
//                $defaultRoleId = OrganizationRole::create($defaultRoleId);
//            }
//
//            $defaultTenantId = 'full_name_tenant';
//            $request->request->add(['created_by' => auth()->user()->id]);
//            $data = $request->except(['password_confirmation']);
//            $data['password'] = Hash::make($request->password);
//            $data['organization_role_id'] = $request->organization_role_id ?? $defaultRoleId;
//            $data['tenant_id'] = $request->tenant_id ?? $defaultTenantId;
//            $organization_signup = $this->model->create($data);
//            if ($organization_signup) {
//                logUserAction(
//                    auth()->user()->id,
//                    auth()->user()->team_id,
//                    $this->panel . ' created successfully!',
//                    [$data]
//                );
//                $request->session()->flash('alert-success', $this->panel . ' created successfully!');
//            } else {
//                logUserAction(
//                    auth()->user()->id,
//                    auth()->user()->team_id,
//                    $this->panel . ' creation failed.',
//                    [$data]
//                );
//                $request->session()->flash('alert-danger', $this->panel . ' creation failed!');
//            }
//        } catch (\Exception $exception) {
//            $request->session()->flash('alert-danger', 'Database Error: ' . $exception->getMessage());
//        }
//        return redirect()->route($this->base_route . '.index');
//    }

    public function store(OrganizationSignupRequest $request)
    {
        //dd($request->all());
        try {
            DB::beginTransaction();
            $request->request->add(['created_by' => auth()->user()->id]);
            $data = $request->except(['password_confirmation']);
            $data['password'] = Hash::make($request->password);
            $organization_signup = $this->model->create($data);
            if (!$organization_signup) {
                throw new \Exception('Failed to create Organization Signup.');
            }
            //  Create Default Tenant
            $tenantName = $request->full_name . '_tenant';
            $defaultTenant = Tenant::create([
                'name' => $tenantName,
                'organization_signup_id' => $organization_signup->id,
                'status' => 1
            ]);
            if (!$defaultTenant) {
                throw new \Exception('Failed to create Tenant.');
            }
            //  Create Default Organization Role (Super Admin)
            $defaultRole = OrganizationRole::create([
                'name' => 'Super Admin',
                'tenant_id' => $defaultTenant->id,
                'status' => 1
            ]);
            if (!$defaultRole) {
                throw new \Exception('Failed to create Organization Role.');
            }
            //  Assign Tenant & Role to User
            $organization_signup->update([
                'organization_role_id' => $defaultRole->id,
                'tenant_id' => $defaultTenant->id
            ]);
            logUserAction(
                auth()->user()->id,
                auth()->user()->team_id,
                $this->panel . ' created successfully!',
                [$data]
            );
            DB::commit();
            $request->session()->flash('alert-success', $this->panel . ' created successfully!');
        } catch (\Exception $exception) {
            DB::rollBack();
            $request->session()->flash('alert-danger', 'Database Error: ' . $exception->getMessage());
        }
        return redirect()->route($this->base_route . '.index');
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $data['record'] = $this->model->find($id);
        if (!$data['record']) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }
        return view(parent::loadView($this->view_path . '.show'), compact('data'));

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View
     */
    public function edit($id): \Illuminate\Http\Response|\Illuminate\Contracts\View\View
    {
        $data['record'] = $this->model->find($id);
        if (!$data['record']) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }
        return view(parent::loadView($this->view_path . '.edit'), compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrganizationSignupRequest $request, $id)
    {
        $data['record'] = $this->model->find($id);
        if (!$data['record']) {
            $request->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . '.index');
        }
        $request->request->add(['updated_by' => auth()->user()->id]);

        try {
            $category = $data['record']->update($request->all());
            if ($category) {
                logUserAction(
                    auth()->user()->id, // User ID
                    auth()->user()->team_id, // Team ID
                    $this->panel . ' updated successfully!',
                    [
                        'data' => $request->all(),
                    ]
                );
                $request->session()->flash('alert-success', $this->panel . ' updated successfully!');
            } else {
                logUserAction(
                    auth()->user()->id,
                    auth()->user()->team_id,
                    $this->panel . ' update failed.',
                    [
                        'data' => $request->all(),
                    ]
                );
                $request->session()->flash('alert-danger', $this->panel . ' update failed!');
            }
        } catch (\Exception $exception) {

            logUserAction(
                auth()->user()->id,
                auth()->user()->team_id,
                'Database Error during ' . $this->panel . ' update',
                [
                    'error' => $exception->getMessage(),
                    'data' => $request->all(),
                ]
            );
            $request->session()->flash('alert-danger', 'Database Error: ' . $exception->getMessage());
        }

        return redirect()->route($this->base_route . '.index');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = $this->model->find($id);
        if (!$record) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . '.index');
        }
        if ($record->delete()) {
            logUserAction(
                auth()->user()->id, // User ID
                auth()->user()->team_id, // Team ID
                $this->panel . ' moved to trash successfully!',
                [
                    'data' => request()->all(),
                ]
            );
            request()->session()->flash('alert-success', $this->panel . ' moved to trash successfully!');
        } else {
            request()->session()->flash('alert-danger', $this->panel . ' trash failed!');
        }
        return redirect()->route($this->base_route . '.index');
    }

    public function trash()
    {
        $data['records'] = $this->model->onlyTrashed()->get();
        return view(parent::loadView($this->view_path . '.trash'), compact('data'));
    }

    public function restore($id)
    {
        $record = $this->model->where('id', $id)->withTrashed()->first();
        if (!$record) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . '.trash');
        }
        if ($record->restore()) {
            logUserAction(
                auth()->user()->id, // User ID
                auth()->user()->team_id, // Team ID
                $this->panel . ' restore successfully!',
                [
                    'data' => request()->all(),
                ]
            );
            request()->session()->flash('alert-success', $this->panel . ' restore successfully!');
        } else {
            request()->session()->flash('alert-danger', $this->panel . ' restore failed!');
        }
        return redirect()->route($this->base_route . '.index');
    }

    public function forceDeleteData($id)
    {
        $record = $this->model->where('id', $id)->withTrashed()->first();
        if (!$record) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . '.index');
        }
        if ($record->forceDelete()) {

            logUserAction(
                auth()->user()->id, // User ID
                auth()->user()->team_id, // Team ID
                $this->panel . ' deleted successfully!',
                [
                    'data' => request()->all(),
                ]
            );
            request()->session()->flash('alert-success', $this->panel . ' deleted successfully!');
        } else {
            request()->session()->flash('alert-danger', $this->panel . ' delete failed!');
        }
        return redirect()->route($this->base_route . '.index');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'password' => 'nullable',
        ]);

        try {
            $organization = OrganizationSignup::findOrFail($request->id);
            $organization->password = Hash::make($request->password);
            $organization->save();
            if ($organization) {
                $request->session()->flash('alert-success', $this->panel . ' Password Change successfully!');
            } else {
                $request->session()->flash('alert-danger', $this->panel . ' creation failed!');
            }
        } catch (\Exception $exception) {
            $request->session()->flash('alert-danger', 'Database Error: ' . $exception->getMessage());
        }
        return redirect()->route($this->base_route . '.index');
    }

//    public function block(Request $request)
//    {
//        $request->validate([
//            'id' => 'required|exists:organization_signup,id',
//            'comment' => 'nullable',
//        ]);
//        try {
//            $organization = OrganizationSignup::findOrFail($request->id);
//            $organization->comment = $request->comment;
//            $organization->status = 0;
//            $organization->save();
//
//            $request->session()->flash('alert-success', 'Organization Blocked successfully!');
//        } catch (\Exception $exception) {
//            $request->session()->flash('alert-danger', 'Database Error: ' . $exception->getMessage());
//        }
//        return redirect()->route($this->base_route . '.index');
//    }

    public function block(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:organization_signup,id',
            'comment' => $request->status == 0 ? 'nullable' : 'nullable',
        ]);
        try {
            $organization = OrganizationSignup::findOrFail($request->id);
            $newStatus = $organization->status == 1 ? 0 : 1;

            if ($newStatus == 0 && !$request->comment) {
                return response()->json(['success' => false, 'message' => 'Comment is required to block.'], 422);
            }
            $organization->status = $newStatus;
            $organization->comment = $request->comment ?? $organization->comment;
            $organization->save();
            return response()->json([
                'success' => true,
                'status' => $organization->status,
                'message' => $newStatus == 1 ? 'Organization Unblocked successfully!' : 'Organization Blocked successfully!',
            ]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => 'Database Error: ' . $exception->getMessage()], 500);
        }
    }
}
