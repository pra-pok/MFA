<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\DM_BaseController;
use App\Models\Organizationemailconfig;
use App\Models\OrganizationSignup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;


class OrganizationemailconfigController extends DM_BaseController
{
    protected $panel = 'Organization Email Configuration';
    protected $base_route = 'organization-email-config';
    protected $view_path = 'admin.components.organization_email_config';
    protected $model;
    protected $table;
    protected $folder = 'organization_email_config';
    public function __construct(Request $request, Organizationemailconfig $organizationemailconfig)
    {
        $this->model = $organizationemailconfig;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->model->with(['createds' => function ($query) {
                $query->select('id', 'username');
            }, 'updatedBy' => function ($query) {
                $query->select('id', 'username');
            }])->orderBy('created_at', 'desc')->get();

            // Decrypt the email-related fields
            $data->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'mail_driver' => $item->mail_driver ? Crypt::decrypt($item->mail_driver) : null,
                    'mail_host' => $item->mail_host ? Crypt::decrypt($item->mail_host) : null,
                    'mail_port' => $item->mail_port ? Crypt::decrypt($item->mail_port) : null,
                    'mail_username' => $item->mail_username ? Crypt::decrypt($item->mail_username) : null,
                    'mail_encryption' => $item->mail_encryption ? Crypt::decrypt($item->mail_encryption) : null,
                    'mail_from_address' => $item->mail_from_address ? Crypt::decrypt($item->mail_from_address) : null,
                    'mail_from_name' => $item->mail_from_name ? Crypt::decrypt($item->mail_from_name) : null,
                    'created_by' => $item->createds->username ?? null,
                    'updated_by' => $item->updatedBy->username ?? null,
                    'created_at' => $item->created_at,
                ];
            });

            return response()->json($data);
        }
        return view(parent::loadView($this->view_path . '.index'));
    }

    public function create()
    {
        return view(parent::loadView($this->view_path . '.create'));
    }
    public function search(Request $request)
    {
        try {
            $query = $request->q;
            $organizations = OrganizationSignup::where('full_name', 'LIKE', "%{$query}%")
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->select('id', 'full_name')
                ->limit(1)
                ->get();
            return response()->json($organizations);
        } catch (\Exception $e) {
            \Log::error('' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'organization_id' => 'required|array|max:1|min:1',
                'organization_id.*' => 'required|exists:organization_signup,id|numeric',
                'mail_driver' => 'required|string|in:smtp,sendmail,mail',
                'mail_host' => 'required|string',
                'mail_port' => 'required|integer|min:1|max:65535',
                'mail_username' => 'required|string|email',
                'mail_password' => 'required|string',
                'mail_encryption' => 'required|string|in:ssl,tls',
                'mail_from_address' => 'required|string|email',
                'mail_from_name' => 'required|string|max:255',
            ]);
            $data['organization_signup_id'] = $validatedData['organization_id'][0];
            $data['mail_driver'] = Crypt::encrypt($validatedData['mail_driver'] ?? null);
            $data['mail_host'] = Crypt::encrypt($validatedData['mail_host'] ?? null);
            $data['mail_port'] = Crypt::encrypt($validatedData['mail_port'] ?? null);
            $data['mail_username'] = Crypt::encrypt($validatedData['mail_username'] ?? null);
            $data['mail_password'] = Crypt::encrypt($validatedData['mail_password'] ?? null);
            $data['mail_encryption'] = Crypt::encrypt($validatedData['mail_encryption'] ?? null);
            $data['mail_from_address'] = Crypt::encrypt($validatedData['mail_from_address'] ?? null);
            $data['mail_from_name'] = Crypt::encrypt($validatedData['mail_from_name'] ?? null);
            $data['created_by'] = auth()->user()->id;
            $data['updated_by'] = auth()->user()->id;
            $data['created_at'] = now();
            $data['updated_at'] = now();
            Organizationemailconfig::insert($data);
            request()->session()->flash('alert-success', $this->panel . ' Successfully Stored');
            return redirect()->route($this->base_route . '.index');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $th) {

            \Log::error($th->getMessage());
            request()->session()->flash('alert-danger', 'Internal Server Error Occurred');
            return redirect()->route($this->base_route . '.index');
        }
    }
    public function show($id)
    {
        $data['record'] = $this->model->with('organizationsignup:id,full_name')->find($id);

        if (!$data['record']) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }
        if ($data['record']) {
            $data['record']->mail_driver = Crypt::decrypt($data['record']->mail_driver);
            $data['record']->mail_host = Crypt::decrypt($data['record']->mail_host);
            $data['record']->mail_port = Crypt::decrypt($data['record']->mail_port);
            $data['record']->mail_username = Crypt::decrypt($data['record']->mail_username);
            $data['record']->mail_password = Crypt::decrypt($data['record']->mail_password);
            $data['record']->mail_encryption = Crypt::decrypt($data['record']->mail_encryption);
            $data['record']->mail_from_address = Crypt::decrypt($data['record']->mail_from_address);
            $data['record']->mail_from_name = Crypt::decrypt($data['record']->mail_from_name);
        }

        return view(parent::loadView($this->view_path . '.show'), compact('data'));
    }
    public function edit($id)
    {
        $data['record'] = $this->model->with('organizationsignup:id,full_name')->find($id);

        if (!$data['record']) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }
        if ($data['record']) {
            $data['record']->mail_driver = Crypt::decrypt($data['record']->mail_driver);
            $data['record']->mail_host = Crypt::decrypt($data['record']->mail_host);
            $data['record']->mail_port = Crypt::decrypt($data['record']->mail_port);
            $data['record']->mail_username = Crypt::decrypt($data['record']->mail_username);
            $data['record']->mail_password = Crypt::decrypt($data['record']->mail_password);
            $data['record']->mail_encryption = Crypt::decrypt($data['record']->mail_encryption);
            $data['record']->mail_from_address = Crypt::decrypt($data['record']->mail_from_address);
            $data['record']->mail_from_name = Crypt::decrypt($data['record']->mail_from_name);
        }

        return view(parent::loadView($this->view_path . '.edit'), compact('data'));
    }
    public function update(Request $request,$id)
    {

        $data['record'] = $this->model->find($id);

        if (!$data['record']) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }

        try {
            $validatedData = $request->validate([
                'organization_id' => 'required|array|max:1|min:1',
                'organization_id.*' => 'required|exists:organization_signup,id|numeric',
                'mail_driver' => 'required|string|in:smtp,sendmail,mail',
                'mail_host' => 'required|string',
                'mail_port' => 'required|integer|min:1|max:65535',
                'mail_username' => 'required|string|email',
                'mail_password' => 'required|string',
                'mail_encryption' => 'required|string|in:ssl,tls',
                'mail_from_address' => 'required|string|email',
                'mail_from_name' => 'required|string|max:255',
            ]);
            $data['organization_signup_id'] = $validatedData['organization_id'][0];
            $data['mail_driver'] = Crypt::encrypt($validatedData['mail_driver'] ?? null);
            $data['mail_host'] = Crypt::encrypt($validatedData['mail_host'] ?? null);
            $data['mail_port'] = Crypt::encrypt($validatedData['mail_port'] ?? null);
            $data['mail_username'] = Crypt::encrypt($validatedData['mail_username'] ?? null);
            $data['mail_password'] = Crypt::encrypt($validatedData['mail_password'] ?? null);
            $data['mail_encryption'] = Crypt::encrypt($validatedData['mail_encryption'] ?? null);
            $data['mail_from_address'] = Crypt::encrypt($validatedData['mail_from_address'] ?? null);
            $data['mail_from_name'] = Crypt::encrypt($validatedData['mail_from_name'] ?? null);
            $data['updated_by'] = auth()->user()->id;
            $data['updated_at'] = now();
            $data['record']->update($data);
            request()->session()->flash('alert-success', $this->panel . ' Successfully Updated');
            return redirect()->route($this->base_route . '.index');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $th) {

            \Log::error($th->getMessage());
            request()->session()->flash('alert-danger', 'Internal Server Error Occurred');
            return redirect()->route($this->base_route . '.index');
        }
    }

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
                $this->panel . ' delete successfully!',
                [
                    'data' => request()->all(),
                ]
            );
            request()->session()->flash('alert-success', $this->panel . ' delete  successfully!');
        } else {
            request()->session()->flash('alert-danger', $this->panel . ' Cannot be deleted!');
        }
        return redirect()->route($this->base_route . '.index');
    }
}
