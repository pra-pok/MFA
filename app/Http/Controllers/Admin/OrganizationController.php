<?php

namespace App\Http\Controllers\Admin;

use App\Dtos\ResponseDTO;
use App\Http\Requests\OrganizationRequest;
use App\Models\AdministrativeArea;
use App\Models\Catalog;
use App\Models\Country;
use App\Models\Course;
use App\Models\Facilities;
use App\Models\GalleryCategory;
use App\Models\Locality;
use App\Models\Organization;
use App\Models\Level;
use App\Models\OrganizationCatalog;
use App\Models\OrganizationCourse;
use App\Models\OrganizationFacilities;
use App\Models\OrganizationGroup;
use App\Models\PageCategory;
use App\Models\Stream;
use App\Models\University;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Utils;
use Illuminate\Support\Facades\Storage;

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
                $data = $this->model->with(relations: [
                    'createds' => function ($query) {
                        $query->select('id', 'username');
                    },
                    'updatedBy' => function ($query) {
                        $query->select('id', 'username');
                    },
                    'locality' => function ($query) {
                        $query->with('administrativeArea.parent.country');
                    },
                ])->orderBy('created_at', 'desc')->get();
                return Utils\ResponseUtil::wrapResponse(new ResponseDTO($data, 'Data retrieved successfully.', 'success'));
            }
        } catch (\Exception $exception) {

            return Utils\ResponseUtil::wrapResponse(new ResponseDTO([], 'An error occurred while retrieving data.', 'error'));
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
        $data['type'] = ['Public' => 'Public', 'Private' => 'Private', 'Community (Aided)' => 'Community (Aided)'
        , 'Community (Managed)' => 'Community (Managed)', 'Community (Teacher Aid)' => 'Community (Teacher Aid)', 'Community (Unaided)' => 'Community (Unaided)'
        , 'Institutional (Private)' => 'Institutional (Private)', 'Institutional (Public)' => 'Institutional (Public)', 'Institutional (Company)' => 'Institutional (Company)'
        , 'Public with religious' => 'Public with religious', 'Madrasa' => 'Madrasa', 'Gumba' => 'Gumba', 'Ashram' => 'Ashram',
            'SOP/FSP' => 'SOP/FSP', 'Community ECD' => 'Community ECD', 'Other' => 'Other'];
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
        $data['country'] = Country::pluck('name', 'id');
        $data['courses'] = Course::pluck('title', 'id');
        $data['page'] = PageCategory::pluck('title', 'id');
        $data['faculty'] = Facilities::where('status', '1')->get();
        $data['Facilities'] = [];
        $data['university'] = University::pluck('title', 'id');
        $data['locality'] = locality::pluck('name', 'id');
        $data['catalog'] = Catalog::where('type', 'College')->pluck('title', 'id');
        $data['groups'] = OrganizationGroup::pluck('title', 'id');
        return view(parent::loadView($this->view_path . '.create'),compact('data'));
    }

    public function getParentsByCountry(Request $request)
    {
        $countryId = $request->id;
        $parents = AdministrativeArea::where('country_id', $countryId)
            ->whereNull('parent_id')
            ->pluck('name', 'id');
        return response()->json(['parents' => $parents]);
    }

    public function getDistrictsByParent(Request $request)
    {
        $parentId = $request->id;
        $districts = AdministrativeArea::where('parent_id', $parentId)->pluck('name', 'id');
        return response()->json(['districts' => $districts]);
    }

    public function getLocalitiesByDistrict(Request $request)
    {
        $districtId = $request->id;
        $localities = Locality::where('administrative_area_id', $districtId)->pluck('name', 'id');
        return response()->json(['localities' => $localities]);
    }
    public function getParentDetailsByLocality(Request $request)
    {
        $localityId = $request->id;
        $locality = Locality::find($localityId);
        $district = $locality->parent;
        $province = $district ? $district->parent : null;
        return response()->json([
            'province' => $province ? $province->id : null,
            'district' => $district ? $district->id : null
        ]);
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
       // dd($request->all());
        try {
            $request->request->add(['created_by' => auth()->user()->id]);
            $request->request->add(['updated_by' => auth()->user()->id]);
            if ($request->hasfile('logo_file')) {
                $fileDirectory = '/data/mfa/' . $this->folder . '/';
                if (!file_exists($fileDirectory)) {
                    mkdir($fileDirectory, 0777, true);
                }
                $logo_file = time() . '.' . $request->file('logo_file')->getClientOriginalExtension();
                $request->file('logo_file')->move($fileDirectory, $logo_file);
                $request->request->add(['logo' => $logo_file]);
            }
            if ($request->hasfile('banner_file')) {
                $fileDirectory = '/data/mfa/organization_banner/';
                if (!file_exists($fileDirectory)) {
                    mkdir($fileDirectory, 0777, true);
                }
                $banner_file = time() . '.' . $request->file('banner_file')->getClientOriginalExtension();
                $request->file('banner_file')->move($fileDirectory, $banner_file);
                $request->request->add(['banner_image' => $banner_file]);
            }
            $organization = $this->model->create($request->all());
            if ($request->has('catalog_id')) {
                $catalogIds = $request->catalog_id;
                $organizationCatalogs = [];
                foreach ($catalogIds as $catalogId) {
                    $organizationCatalogs[] = [
                        'organization_id' => $organization->id,
                        'catalog_id' => $catalogId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                OrganizationCatalog::insert($organizationCatalogs);
            }
            logUserAction(
                auth()->user()->id,
                auth()->user()->team_id,
                $this->panel . ' created successfully!',
                [
                    $request->all(),
                ]
            );
            return Utils\ResponseUtil::wrapResponse(new ResponseDTO($organization->id, 'Data retrieved successfully.', 'success'));
        } catch (\Exception $exception) {
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
            'organizationCourses' , 'organizationPages' , 'organizationfacilities' ])
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
        $data['record'] = $this->model->with(['organizationGalleries', ])
        ->find($id);
        if (!$data['record']) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }
        $data['area'] = AdministrativeArea::pluck('name', 'id');
        $data['type'] = ['Public' => 'Public', 'Private' => 'Private', 'Community (Aided)' => 'Community (Aided)'
            , 'Community (Managed)' => 'Community (Managed)', 'Community (Teacher Aid)' => 'Community (Teacher Aid)', 'Community (Unaided)' => 'Community (Unaided)'
            , 'Institutional (Private)' => 'Institutional (Private)', 'Institutional (Public)' => 'Institutional (Public)', 'Institutional (Company)' => 'Institutional (Company)'
            , 'Public with religious' => 'Public with religious', 'Madrasa' => 'Madrasa', 'Gumba' => 'Gumba', 'Ashram' => 'Ashram',
            'SOP/FSP' => 'SOP/FSP', 'Community ECD' => 'Community ECD', 'Other' => 'Other'];
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
        $data['courses'] = Course::pluck('title', 'id');
        $data['country'] = Country::pluck('name', 'id');
        $data['organization_courses'] = $data['record']->organizationCourses;
        $data['page'] = PageCategory::pluck('title', 'id');
        $data['organization_pages'] = $data['record']->organizationPages;
        $data['faculty'] = Facilities::where('status', '1')->get();
        $data['Facilities'] = OrganizationFacilities::where('organization_id', $id)->pluck('facility_id')->toArray();
        $data['university'] = University::pluck('title', 'id');
        $data['locality'] = locality::pluck('name', 'id');
        $data['catalog'] = Catalog::where('type', 'College')->pluck('title', 'id');
        $data['selectedCatalogIds'] = $data['record']->organizationCatalog->pluck('catalog_id');
        $data['groups'] = OrganizationGroup::pluck('title', 'id');
        $data['organization_members'] = $data['record']->organizationMembers;
        return view(parent::loadView($this->view_path . '.edit'), compact('data'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrganizationRequest $request, $id)
    {
        $data['record'] = $this->model->find($id);
        if (!$data['record']) {
            $request->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . '.index');
        }
        if ($request->hasfile('logo_file')) {
            $fileDirectory = '/data/mfa/' . $this->folder . '/';
            if (!file_exists($fileDirectory)) {
                mkdir($fileDirectory, 0777, true);
            }
            $logo_file = time() . '.' . $request->file('logo_file')->getClientOriginalExtension();
            $request->file('logo_file')->move($fileDirectory, $logo_file);
            if ($data['record']->logo && file_exists(($fileDirectory . $data['record']->logo))) {
                unlink(($fileDirectory . $data['record']->logo));
            }
            $request->request->add(['logo' => $logo_file]);
        } else {
            $request->request->add(['logo' => $data['record']->logo]);
        }
        if ($request->hasfile('banner_file')) {
            $fileDirectory = '/data/mfa/organization_banner/';
            if (!file_exists($fileDirectory)) {
                mkdir($fileDirectory, 0777, true);
            }
            $banner_file = time() . '.' . $request->file('banner_file')->getClientOriginalExtension();
            $request->file('banner_file')->move($fileDirectory, $banner_file);

            if ($data['record']->banner_image && file_exists(($fileDirectory . $data['record']->banner_image))) {
                unlink(($fileDirectory . $data['record']->banner_image));
            }
            $request->request->add(['banner_image' => $banner_file]);
        } else {
            $request->request->add(['banner_image' => $data['record']->banner_image]);
        }
        $request->request->add(['updated_by' => auth()->user()->id]);

        try {
            DB::beginTransaction();

            $category = $data['record']->update($request->all());
            if ($request->has('catalog_id')) {
                $existingDocuments = $data['record']->organizationCatalog()->get();
                $catalogIds = $request->catalog_id;

                $catalogIdsToRemove = $existingDocuments->pluck('catalog_id')->diff($catalogIds);
                foreach ($catalogIdsToRemove as $catalogId) {
                    $data['record']->organizationCatalog()->where('catalog_id', $catalogId)->delete();
                }
                foreach ($catalogIds as $catalogId) {
                    $existingDocument = $existingDocuments->where('catalog_id', $catalogId)->first();

                    if ($existingDocument) {
                        $existingDocument->update([
                            'organization_id' => $data['record']->id,
                            'catalog_id' => $catalogId,
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Create new catalog
                        $data['record']->organizationCatalog()->create([
                            'organization_id' => $data['record']->id,
                            'catalog_id' => $catalogId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            DB::commit();
            logUserAction(
                auth()->user()->id, // User ID
                auth()->user()->team_id, // Team ID
                $this->panel . ' updated successfully!',
                [
                    'data' => $request->all(),
                ]
            );

            return Utils\ResponseUtil::wrapResponse(new ResponseDTO($data, 'Data updated successfully.', 'success'));
        } catch (\Exception $exception) {
            DB::rollback();
            logUserAction(
                auth()->user()->id,
                auth()->user()->team_id,
                'Database Error during ' . $this->panel . ' update',
                [
                    'error' => $exception->getMessage(),
                    'data' => $request->all(),
                ]
            );

            // Return error response
            return response()->json(['success' => false, 'error' => $exception->getMessage()], 500);
        }
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
