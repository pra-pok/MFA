<?php

namespace App\Http\Controllers\Admin;

use App\Dtos\ResponseDTO;
use App\Models\AdministrativeArea;
use App\Models\Country;
use App\Models\GalleryCategory;
use App\Models\OrganizationGroup;
use App\Models\OrganizationMember;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\Log;
use App\Utils;
class OrganizationMemberController extends DM_BaseController
{
    protected $panel = 'Organization Member';
    protected $base_route = 'organization-member';
    protected $view_path = 'admin.components.organization_member';
    protected $model;
    protected $table;
    protected $folder = 'organization_member';

    public function __construct(Request $request, OrganizationMember $organization_member)
    {
        $this->model = $organization_member;
    }
    /**
     * Display a listing of the resource.
     *@return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->model->with(['createds' => function($query) {
                $query->select('id', 'username');
            }, 'updatedBy' => function($query) {
                $query->select('id', 'username');
            }])->get();
            return response()->json($data);
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
        $data['groups'] = OrganizationGroup::pluck('name', 'id');
        return view(parent::loadView($this->view_path . '.create'),compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */

    public function store(Request $request)
    {

        try {
            $request->validate([
                'organization_id' => 'required|integer',
                'organization_group_id' => 'required|array',
                'name' => 'nullable|array',
                'rank' => 'nullable|array',
                'designation' => 'nullable|array',
                'photo' => 'nullable|array',
                'bio' => 'nullable|array',
                'status' => 'nullable|array',
            ]);
            $organization_id = $request->input('organization_id');
            $organization_group_ids = array_values($request->input('organization_group_id', []));
            $names = array_values($request->input('name', []));
            $ranks = array_values($request->input('rank', []));
            $designations = array_values($request->input('designation', []));
            $bios = array_values($request->input('bio', []));
            $statuses = array_values($request->input('status', []));
            $created_by = auth()->id();
            $updated_by = auth()->id();
            $photos = [];
            $organizationMembers = [];
            if ($request->hasFile('photo_file')) {
                foreach ($request->file('photo_file') as $index => $file) {
                    $fileDirectory = '/data/mfa/' . $this->folder . '/';
                    if (!file_exists($fileDirectory)) {
                        mkdir($fileDirectory, 0777, true);
                    }
                    $filename = time() . '-' . $index . '.' . $file->getClientOriginalExtension();
                    $file->move($fileDirectory, $filename);
                    $photos[$index] = $filename;
                }
            }
            foreach ($organization_group_ids as $index => $organization_group_id) {
                if (!isset($organization_group_id)) {
                    continue;
                }
                $name = isset($names[$index]) ? $names[$index] : null;
                $rank = isset($ranks[$index]) ? $ranks[$index] : null;
                $designation = isset($designations[$index]) ? $designations[$index] : null;
                $bio = isset($bios[$index]) ? $bios[$index] : null;
                $status = isset($statuses[$index]) && $statuses[$index] == '1' ? 1 : 1;
                $photo = $photos[$index] ?? null;
                $organizationMember = OrganizationMember::where([
                    'organization_id' => $organization_id,
                    'organization_group_id' => $organization_group_id,
                ])->first();
                if (isset($request->id[$index]) && $request->id[$index] !== null) {
                    $organizationMember = OrganizationMember::find($request->id[$index]);
                    if ($organizationMember) {
                        $organizationMember->update([
                            'organization_id' => $organization_id,
                            'organization_group_id' => $organization_group_id,
                            'name' => $name,
                            'rank' => $rank,
                            'photo' => $photo ?? $organizationMember->photo, // Keep existing photo if not changed
                            'designation' => $designation,
                            'bio' => $bio,
                            'updated_by' => $updated_by,
                            'status' => $status,
                        ]);
                    }
                } else {
                    OrganizationMember::create([
                        'organization_id' => $organization_id,
                        'organization_group_id' => $organization_group_id,
                        'name' => $name,
                        'rank' => $rank,
                        'photo' => $photo,
                        'designation' => $designation,
                        'bio' => $bio,
                        'created_by' => $created_by,
                        'updated_by' => $updated_by,
                        'status' => $status,
                    ]);
                }
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Members stored/updated successfully.',
                'data' => $organizationMembers
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving/updating member data.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View
     */
    public function edit($id): \Illuminate\Http\Response|\Illuminate\Contracts\View\View
    {
        $data['area'] = AdministrativeArea::pluck('name', 'id');
        $data['type'] = ['Public' => 'Public', 'Private' => 'Private', 'Community' => 'Community'];
        $data['gallery'] = GalleryCategory::pluck('name', 'id');
        $data['organization'] = Organization::pluck('name', 'id');
        $data['gallery_type'] = ['Video' => 'Video', 'Image' => 'Image'];
        $data['social'] = collect([
            ['name' => 'Facebook', 'icon' => 'bx bxl-facebook'],
            ['name' => 'Instagram', 'icon' => 'bx bxl-instagram'],
            ['name' => 'Twitter', 'icon' => 'bx bxl-twitter'],
            ['name' => 'Youtube', 'icon' => 'bx bxl-youtube'],
            ['name' => 'Linkedin', 'icon' => 'bx bxl-linkedin'],
            ['name' => 'Tiktok', 'icon' => 'bx bxl-tiktok'],
        ])->map(function ($social) use ($data) {
            $existing = $data['record']->socialMediaLinks->firstWhere('name', $social['name']);
            $social['url'] = $existing->url ?? null;
            return $social;
        });
        if (!$data['record']) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }
        return view(parent::loadView($this->view_path . '.edit'), compact('data'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
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

    public function permanentDelete($id)
    {
        $record = OrganizationMember::find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found.'
            ], 404);
        }

        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully.'
        ]);
    }

}
