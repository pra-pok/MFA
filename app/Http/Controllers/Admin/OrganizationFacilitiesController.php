<?php

namespace App\Http\Controllers\Admin;

use App\Dtos\ResponseDTO;
use App\Models\AdministrativeArea;
use App\Models\Facilities;
use App\Models\GalleryCategory;
use App\Models\OrganizationFacilities;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\Log;
use App\Utils;
class OrganizationFacilitiesController extends DM_BaseController
{
    protected $panel = 'Organization Organization Facilities';
    protected $base_route = 'organization_facilities';
    protected $view_path = 'admin.components.organization_facilities';
    protected $model;
    protected $table;
    protected $folder = 'organization_facilities';



    public function __construct(Request $request, OrganizationFacilities $organization_facilities)
    {
        $this->model = $organization_facilities;
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
            }])->orderBy('created_at', 'desc')->get();
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
        $data['area'] = AdministrativeArea::pluck('name', 'id');
        $data['type'] = ['Public' => 'Public', 'Private' => 'Private', 'Community' => 'Community'];
        $data['gallery'] = GalleryCategory::pluck('name', 'id');
        $data['organization'] = Organization::pluck('name', 'id');
        $data['gallery_type'] = ['Video' => 'Video', 'Image' => 'Image'];
        $data['social'] = [
            'Facebook' => ['name' => 'Facebook', 'icon' => 'bx bxl-facebook'],
            'Instagram' => ['name' => 'Instagram', 'icon' => 'bx bxl-instagram'],
            'Twitter' => ['name' => 'Twitter', 'icon' => 'bx bxl-twitter'],
            'Youtube' => ['name' => 'Youtube', 'icon' => 'bx bxl-youtube'],
            'Linkedin' => ['name' => 'Linkedin', 'icon' => 'bx bxl-linkedin'],
            'Tiktok' => ['name' => 'Tiktok', 'icon' => 'bx bxl-tiktok'],
        ];
        $data['faculty'] = Facilities::where('status', '1')->get();
        return view(parent::loadView($this->view_path . '.create'),compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
//    public function store(Request $request)
//    {
//        try {
//            $organization_id = $request->input('organization_id');
//            $faculty_id = $request->input('facility_id');
//            $created_by = auth()->user()->id;
//            $updated_by = auth()->user()->id;
//
//            foreach ($facility_id as $index => $url) {
//                $organizationFaculty = OrganizationFacilities::where('organization_id', $organization_id)
//                    ->where('facility_id', $facility_id[$index])
//                    ->first();
//                if ($organizationFaculty) {
//                    $organizationFaculty->facility_id = $facility_id;
//                    $organizationFaculty->updated_by = $updated_by;
//                    $organizationFaculty->save();
//                } else {
//                    $organizationFaculty = new OrganizationFacilities();
//                    $organizationFaculty->organization_id = $organization_id;
//                    $organizationFaculty->facility_id = $facility_id[$index];
//                    $organizationFaculty->created_by = $created_by;
//                    $organizationFaculty->updated_by = $updated_by;
//                    $organizationFaculty->save();
//                }
//            }
//
//            return Utils\ResponseUtil::wrapResponse(new ResponseDTO($organizationFaculty, 'Social Media items stored/updated successfully.', 'success'));
//        } catch (\Exception $exception) {
//            Log::error('Error saving/updating organization Social Media data', ['error' => $exception->getMessage()]);
//            return Utils\ResponseUtil::wrapResponse(new ResponseDTO($organizationFaculty, 'An error occurred while saving/updating Social Media items.', 'error'));
//        }
//    }
    public function store(Request $request)
    {
      //  dd($request->all());
        try {
            $organization_id = $request->input('organization_id');
            $facility_ids = $request->input('facility_id', []);
            $created_by = auth()->user()->id;
            $updated_by = auth()->user()->id;
            $facility_ids = array_filter($facility_ids, function ($value) {
                return $value != "0";
            });
            $savedFacilities = [];
            foreach ($facility_ids as $facility_id) {
                $organizationFacility = OrganizationFacilities::where('organization_id', $organization_id)
                    ->where('facility_id', $facility_id)
                    ->first();
                if ($organizationFacility) {
                    $organizationFacility->updated_by = $updated_by;
                    $organizationFacility->save();
                } else {
                    $organizationFacility = new OrganizationFacilities();
                    $organizationFacility->organization_id = $organization_id;
                    $organizationFacility->facility_id = $facility_id;
                    $organizationFacility->created_by = $created_by;
                    $organizationFacility->updated_by = $updated_by;
                    $organizationFacility->save();
                }

                $savedFacilities[] = $organizationFacility;
            }
            return Utils\ResponseUtil::wrapResponse(new ResponseDTO($savedFacilities, 'Facilities stored/updated successfully.', 'success'));
        } catch (\Exception $exception) {
            return Utils\ResponseUtil::wrapResponse(new ResponseDTO(null, 'An error occurred while saving/updating facilities.', 'error'));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrganizationGalleryRequest  $request, $id)
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
                // Custom log for success
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
            $request->session()->flash('alert-danger', 'Database Error: ' . $exception->getMessage());
        }

        return redirect()->route($this->base_route . '.index');
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
