<?php

namespace App\Http\Controllers\Admin;

use App\Dtos\ResponseDTO;
use App\Http\Requests\OrganizationPageRequest;
use App\Models\AdministrativeArea;
use App\Models\Country;
use App\Models\GalleryCategory;
use App\Models\OrganizationPage;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\Log;
use App\Utils;
class OrganizationPageController extends DM_BaseController
{
    protected $panel = 'Organization Page';
    protected $base_route = 'organization_page';
    protected $view_path = 'admin.components.organization_page';
    protected $model;
    protected $table;
    protected $folder = 'organization_page';



    public function __construct(Request $request, OrganizationPage $organization_page)
    {
        $this->model = $organization_page;
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
        $data['country'] = Country::pluck('name', 'id');
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
                'organization_id' => 'required',
                'page_category_id' => 'nullable|array',
                'description' => 'nullable|array',
                'status' => 'nullable|array',
            ]);

            $organization_id = $request->input('organization_id');
            $page_category_ids = $request->input('page_category_id', []);
            $descriptions = $request->input('description', []);
            $statuses = $request->input('status', []);
            $created_by = auth()->user()->id;
            $updated_by = auth()->user()->id;

            $organizationPages = [];

            foreach ($page_category_ids as $index => $page_category_id) {
                $description = $descriptions[$index] ?? null;
                $status_value = $statuses[$index] ?? 0; // Default to 0 if not provided

                $organizationPage = OrganizationPage::updateOrCreate(
                    [
                        'organization_id' => $organization_id,
                        'page_category_id' => $page_category_id
                    ],
                    [
                        'description' => $description,
                        'status' => $status_value,
                        'updated_by' => $updated_by,
                        'created_by' => $created_by
                    ]
                );

                $organizationPages[] = $organizationPage;
            }

            return Utils\ResponseUtil::wrapResponse(
                new ResponseDTO($organizationPages, 'Pages stored/updated successfully.', 'success')
            );

        } catch (\Exception $exception) {
            Log::error('Error saving/updating organization page data', ['error' => $exception->getMessage()]);
            return Utils\ResponseUtil::wrapResponse(
                new ResponseDTO([], 'An error occurred while saving/updating page data.', 'error')
            );
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
    public function update(OrganizationPageRequest  $request, $id)
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

    public function permanentDelete($id)
    {
        $record = OrganizationPage::find($id);

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
