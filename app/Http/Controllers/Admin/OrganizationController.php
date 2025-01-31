<?php

namespace App\Http\Controllers\Admin;

use App\Dtos\ResponseDTO;
use App\Http\Requests\OrganizationRequest;
use App\Models\AdministrativeArea;
use App\Models\Country;
use App\Models\Course;
use App\Models\Facilities;
use App\Models\GalleryCategory;
use App\Models\Organization;
use App\Models\Level;
use App\Models\OrganizationCourse;
use App\Models\OrganizationFacilities;
use App\Models\PageCategory;
use App\Models\Stream;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Utils;

class OrganizationController extends DM_BaseController
{
    protected $panel = 'College / School';
    protected $base_route = 'organization';
    protected $view_path = 'admin.components.organization';
    protected $model;
    protected $table;
    protected $folder = 'organization';



    public function __construct(Request $request, Organization $organization)
    {
        $this->model = $organization;
    }
    /**
     * Display a listing of the resource.
     *@return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = $this->model->with([
                    'createds' => function ($query) {
                        $query->select('id', 'username');
                    },
                    'updatedBy' => function ($query) {
                        $query->select('id', 'username');
                    }
                ])->get();

                return Utils\ResponseUtil::wrapResponse(new ResponseDTO($data, 'Data retrieved successfully.', 'success'));
            }
        } catch (\Exception $exception) {
            \Log::error('Error in index method: ' . $exception->getMessage(), [
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'data' => [],
                'message' => 'An error occurred while retrieving data.',
                'status' => 'error'
            ], 500);
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
        $data['area'] = AdministrativeArea::pluck('name', 'id');
        $data['type'] = ['Public' => 'Public', 'Private' => 'Private', 'Community' => 'Community'];
        $data['gallery'] = GalleryCategory::pluck('name', 'id');
        $data['organization'] = Organization::pluck('name', 'id');
        $data['gallery_type'] = ['Video' => 'Video', 'Image' => 'Image'];
       // $data['social'] = DB::table('social_medias')->get();
        $data['social'] = [
            'Facebook' => ['name' => 'Facebook', 'icon' => 'bx bxl-facebook'],
            'Instagram' => ['name' => 'Instagram', 'icon' => 'bx bxl-instagram'],
            'Twitter' => ['name' => 'Twitter', 'icon' => 'bx bxl-twitter'],
            'Youtube' => ['name' => 'Youtube', 'icon' => 'bx bxl-youtube'],
            'Linkedin' => ['name' => 'Linkedin', 'icon' => 'bx bxl-linkedin'],
            'Tiktok' => ['name' => 'Tiktok', 'icon' => 'bx bxl-tiktok'],
        ];
        $data['courses'] = Course::pluck('title', 'id');
        $data['country'] = Country::pluck('name', 'id');
        $data['page'] = PageCategory::pluck('title', 'id');
        $data['faculty'] = Facilities::where('status', '1')->get();
        $data['Facilities'] = [];
        return view(parent::loadView($this->view_path . '.create'),compact('data'));
    }
    public function getParentsByCountry(Request $request)
    {
        $countryId = $request->id;
        $parents = AdministrativeArea::where('country_id', $countryId)->whereNull('parent_id')->pluck('name', 'id');
        return response()->json(['parents' => $parents]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function store(OrganizationRequest $request)
    {
        try {
            $request->request->add(['created_by' => auth()->user()->id]);

            if ($request->hasfile('logo_file')) {
                $logo_file = time() . '.' . $request->file('logo_file')->getClientOriginalExtension();
                $request->file('logo_file')->move('images/' . $this->folder . '/', $logo_file);
                $request->request->add(['logo' => $logo_file]);
            }

            if ($request->hasfile('banner_file')) {
                $banner_file = time() . '.' . $request->file('banner_file')->getClientOriginalExtension();
                $request->file('banner_file')->move('images/' . $this->folder . '/banner/', $banner_file);
                $request->request->add(['banner_image' => $banner_file]);
            }

            $organization = $this->model->create($request->all());

            return Utils\ResponseUtil::wrapResponse(new ResponseDTO($organization->id, 'Data retrieved successfully.', 'success'));
        } catch (\Exception $exception) {
            Log::error('Error saving organization data', ['error' => $exception->getMessage()]);
            return response()->json(['success' => false, 'error' => $exception->getMessage()], 500);
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
//        $data['record'] = $this->model->find($id);
        $data['record'] = $this->model->with(['createds', 'updatedBy', 'socialMediaLinks', 'organizationGalleries' ,
            'organizationCourses' , 'organizationPages' , 'organizationfacilities' ])  // Eager load any relationships
        ->find($id);

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
        // Load the record first
        $data['record'] = $this->model->with(['organizationGalleries', ])
        ->find($id);
        if (!$data['record']) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }
        // Prepare other data
        $data['area'] = AdministrativeArea::pluck('name', 'id');
        $data['type'] = ['Public' => 'Public', 'Private' => 'Private', 'Community' => 'Community'];
        $data['gallery'] = GalleryCategory::pluck('name', 'id');
        $data['organization'] = Organization::pluck('name', 'id');
        $data['gallery_type'] = ['Video' => 'Video', 'Image' => 'Image'];
        // Prepare social media links
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
        $data['courses'] = Course::pluck('title', 'id');
        $data['country'] = Country::pluck('name', 'id');
        $data['organization_courses'] = $data['record']->organizationCourses;
        $data['page'] = PageCategory::pluck('title', 'id');
        $data['organization_pages'] = $data['record']->organizationPages;
        $data['faculty'] = Facilities::where('status', '1')->get();
        $data['Facilities'] = OrganizationFacilities::where('organization_id', $id)->pluck('facility_id')->toArray();
        return view(parent::loadView($this->view_path . '.edit'), compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrganizationRequest  $request, $id)
    {
        $data['record'] = $this->model->find($id);
        if (!$data['record']) {
            $request->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . '.index');
        }
        if ($request->hasfile('logo_file')) {
            $logo_file = time() . '.' . $request->file('logo_file')->getClientOriginalExtension();

            $request->file('logo_file')->move('images/' . $this->folder . '/', $logo_file);
            $request->request->add(['logo' => $logo_file]);
            if ($data['record']->logo && file_exists(public_path('images/' . $this->folder . '/' . $data['record']->logo))) {
                unlink(public_path('images/' . $this->folder . '/' . $data['record']->logo));
            }
        } else {
            $request->request->add(['logo' => $data['record']->logo]);
        }

        if ($request->hasfile('banner_file')) {
            $banner_file = time() . '.' . $request->file('banner_file')->getClientOriginalExtension();

            $request->file('banner_file')->move('images/' . $this->folder . '/banner/', $banner_file);
            $request->request->add(['banner_image' => $banner_file]);
            if ($data['record']->banner_image && file_exists(public_path('images/' . $this->folder . '/banner/' . $data['record']->banner_image))) {
                unlink(public_path('images/' . $this->folder . '/banner/' . $data['record']->banner_image));
            }
        } else {
            $request->request->add(['banner_image' => $data['record']->banner_image]);
        }
        $request->request->add(['updated_by' => auth()->user()->id]);

        try {
            $category = $data['record']->update($request->all());
            if ($category) {
                // Custom log for success
                logUserAction(
                    auth()->user()->id, // User ID
                    auth()->user()->team_id, // Team ID
                    $this->panel . ' updated successfully!',
                    [
                        'data' => $request->all(),
                    ]
                );
//                $request->session()->flash('alert-success', $this->panel . ' updated successfully!');
                return Utils\ResponseUtil::wrapResponse(new ResponseDTO($data, 'Data updated successfully.', 'success'));
            } else {
                // Custom log for failure
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
            // Custom log for errors
            logUserAction(
                auth()->user()->id,
                auth()->user()->team_id,
                'Database Error during ' . $this->panel . ' update',
                [
                    'error' => $exception->getMessage(),
                    'data' => $request->all(),
                ]
            );
            return response()->json(['success' => false, 'error' => $exception->getMessage()], 500);
        }

//        return redirect()->route($this->base_route . '.index');
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




}
