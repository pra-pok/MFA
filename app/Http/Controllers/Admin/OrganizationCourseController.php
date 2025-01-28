<?php

namespace App\Http\Controllers\Admin;

use App\Dtos\ResponseDTO;
use App\Http\Requests\OrganizationGalleryRequest;
use App\Models\AdministrativeArea;
use App\Models\Country;
use App\Models\GalleryCategory;
use App\Models\OrganizationCourse;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\Log;
use App\Utils;
class OrganizationCourseController extends DM_BaseController
{
    protected $panel = 'Organization Course';
    protected $base_route = 'organization_course';
    protected $view_path = 'admin.components.organization_course';
    protected $model;
    protected $table;
    protected $folder = 'organization_course';



    public function __construct(Request $request, OrganizationCourse $organization_course)
    {
        $this->model = $organization_course;
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


//    public function store(Request $request)
//    {
//       // dd($request->all());
//        try {
//            // Validate the incoming request
//            $request->validate([
//                'organization_id' => 'required|exists:organizations,id',
//                'course_id' => 'required|array',
//                'start_fee' => 'required|array',
//                'end_fee' => 'required|array',
//                'description' => 'nullable|array',
//            ]);
//            $organization_id = $request->input('organization_id');
//            $course_id = $request->input('course_id');
//            $start_fees = $request->input('start_fee');
//            $end_fees = $request->input('end_fee');
//            $descriptions = $request->input('description');
//            $created_by = auth()->user()->id;
//            $updated_by = auth()->user()->id;
//            $organizationCourses = [];
//            // Loop through the courses and fees
//            foreach ($start_fees as $index => $start_fee) {
//                if (!isset($course_id[$index], $start_fees[$index], $end_fees[$index])) {
//                    continue;
//                }
//                // Ensure we get the description if it's provided
//                $description = isset($descriptions[$index]) ? $descriptions[$index] : null; // Null if not provided
//                // Check if the course already exists for the organization
//                $organizationCourse = OrganizationCourse::where('organization_id', $organization_id)
//                    ->where('course_id', $course_id[$index])
//                    ->where('start_fee', $start_fee)
//                    ->first();
//                // If it exists, update it, otherwise create a new record
//                if ($organizationCourse) {
//                    $organizationCourse->update([
//                        'end_fee' => $end_fees[$index],
//                        'description' => $description,
//                        'updated_by' => $updated_by,
//                    ]);
//                    $organizationCourses[] = $organizationCourse;
//                } else {
//                    $organizationCourse = OrganizationCourse::create([
//                        'organization_id' => $organization_id,
//                        'course_id' => $course_id[$index],
//                        'start_fee' => $start_fee,
//                        'end_fee' => $end_fees[$index],
//                        'description' => $description,
//                        'created_by' => $created_by,
//                        'updated_by' => $updated_by,
//                    ]);
//                    $organizationCourses[] = $organizationCourse;
//                }
//            }
//            return Utils\ResponseUtil::wrapResponse(
//                new ResponseDTO($organizationCourses, 'Courses stored/updated successfully.', 'success')
//            );
//        } catch (\Exception $exception) {
//            Log::error('Error saving/updating organization course data', ['error' => $exception->getMessage()]);
//            return Utils\ResponseUtil::wrapResponse(
//                new ResponseDTO([], 'An error occurred while saving/updating course data.', 'error')
//            );
//        }
//    }

    public function store(Request $request)
    {
     //   dd($request->all());  // Remove this after debugging
        try {
            $request->validate([
                'organization_id' => 'required',
                'course_id' => 'required|array',
                'start_fee' => 'nullable|array',
                'end_fee' => 'nullable|array',
                'description' => 'nullable|array',
            ]);

            $organization_id = $request->input('organization_id');
            $course_id = $request->input('course_id');
            $start_fees = $request->input('start_fee');
            $end_fees = $request->input('end_fee');
            $descriptions = $request->input('description');
            $created_by = auth()->user()->id;
            $updated_by = auth()->user()->id;
            $organizationCourses = [];

            // Ensure all arrays have the same length and clean up invalid data
            foreach ($start_fees as $index => $start_fee) {
                // Remove the entry if any field is invalid or empty
                if (is_null($start_fee) || is_null($end_fees[$index]) || empty($start_fee) || empty($end_fees[$index]) || empty($descriptions[$index])) {
                    unset($start_fees[$index], $end_fees[$index], $descriptions[$index], $course_id[$index]);
                }
            }

            // Loop through and create or update records
            foreach ($course_id as $index => $course) {
                // Ensure there is data for the current entry
                if (!isset($start_fees[$index], $end_fees[$index], $course_id[$index])) {
                    continue; // Skip if any required field is missing
                }

                $description = isset($descriptions[$index]) ? $descriptions[$index] : null;

                // Check if the course already exists in the database
                $organizationCourse = OrganizationCourse::where('organization_id', $organization_id)
                    ->where('course_id', $course_id[$index])
                    ->where('start_fee', $start_fees[$index])
                    ->first();

                if ($organizationCourse) {
                    // Update existing course record
                    $organizationCourse->update([
                        'end_fee' => $end_fees[$index],
                        'description' => $description,
                        'updated_by' => $updated_by,
                    ]);
                    $organizationCourses[] = $organizationCourse;
                } else {
                    // Create new course record
                    $organizationCourse = OrganizationCourse::create([
                        'organization_id' => $organization_id,
                        'course_id' => $course_id[$index],
                        'start_fee' => $start_fees[$index],
                        'end_fee' => $end_fees[$index],
                        'description' => $description,
                        'created_by' => $created_by,
                        'updated_by' => $updated_by,
                    ]);
                    $organizationCourses[] = $organizationCourse;
                }
            }

            return Utils\ResponseUtil::wrapResponse(
                new ResponseDTO($organizationCourses, 'Courses stored/updated successfully.', 'success')
            );
        } catch (\Exception $exception) {
            Log::error('Error saving/updating organization course data', ['error' => $exception->getMessage()]);
            return Utils\ResponseUtil::wrapResponse(
                new ResponseDTO([], 'An error occurred while saving/updating course data.', 'error')
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
