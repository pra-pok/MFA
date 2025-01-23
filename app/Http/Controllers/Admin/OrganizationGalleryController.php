<?php

namespace App\Http\Controllers\Admin;

use App\Dtos\ResponseDTO;
use App\Http\Requests\OrganizationGalleryRequest;
use App\Models\AdministrativeArea;
use App\Models\GalleryCategory;
use App\Models\OrganizationGallery;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\Log;
use App\Utils;
class OrganizationGalleryController extends DM_BaseController
{
    protected $panel = 'Organization Gallery';
    protected $base_route = 'organization_gallery';
    protected $view_path = 'admin.components.organization-gallery';
    protected $model;
    protected $table;
    protected $folder = 'organization-gallery';



    public function __construct(Request $request, OrganizationGallery $organization_gallery)
    {
        $this->model = $organization_gallery;
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
        $data['gallery'] = GalleryCategory::pluck('name', 'id');
        $data['organization'] = Organization::pluck('name', 'id');
        $data['type'] = ['Video' => 'Video', 'Image' => 'Image'];
        return view(parent::loadView($this->view_path . '.create'),compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function store(OrganizationGalleryRequest $request)
    {
        try {
            $organization_id = $request->input('organization_id');
            foreach ($request->input('gallery_category_id') as $index => $category_id) {
                $type = $request->input('type')[$index];
                if ($type == '1' && $request->hasFile('media_file')) {
                    foreach ($request->file('media_file') as $fileIndex => $file) {
                        $media_file = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('images/' . $this->folder . '/'), $media_file);
                        $this->model::create([
                            'gallery_category_id' => $category_id,
                            'type' => $type,
                            'caption' => $request->input('caption')[$index],
                            'rank' => $request->input('rank')[$index],
                            'media' => $media_file,
                            'organization_id' => $organization_id,
                            'created_by' => auth()->user()->id,
                        ]);
                    }
                }
                if ($type == '0') {
                    $this->model::create([
                        'gallery_category_id' => $category_id,
                        'type' => $type,
                        'caption' => $request->input('caption')[$index],
                        'rank' => $request->input('rank')[$index],
                        'media' => $request->input('media')[$index],
                        'organization_id' => $organization_id,
                        'created_by' => auth()->user()->id,
                    ]);
                }
            }
            return Utils\ResponseUtil::wrapResponse(new ResponseDTO(null, 'Gallery items uploaded successfully.', 'success'));
        } catch (\Exception $exception) {
            Log::error('Error saving organization gallery data', ['error' => $exception->getMessage()]);
            return Utils\ResponseUtil::wrapResponse(new ResponseDTO(null, 'An error occurred while saving gallery items.', 'error'));
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
        $data['gallery'] = GalleryCategory::pluck('name', 'id');
        $data['organization'] = Organization::pluck('name', 'id');
        $data['type'] = ['Video' => 'Video', 'Image' => 'Image'];
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
//    public function update(OrganizationGalleryRequest  $request, $id)
//    {
//        $data['record'] = $this->model->find($id);
//        if (!$data['record']) {
//            $request->session()->flash('alert-danger', 'Invalid Request');
//            return redirect()->route($this->base_route . '.index');
//        }
//
//     //   $request->request->add(['updated_by' => auth()->user()->id]);
//
////        try {
////            $category = $data['record']->update($request->all());
////            if ($category) {
////                // Custom log for success
////                logUserAction(
////                    auth()->user()->id, // User ID
////                    auth()->user()->team_id, // Team ID
////                    $this->panel . ' updated successfully!',
////                    [
////                        'data' => $request->all(),
////                    ]
////                );
////                $request->session()->flash('alert-success', $this->panel . ' updated successfully!');
////            } else {
////                // Custom log for failure
////                logUserAction(
////                    auth()->user()->id,
////                    auth()->user()->team_id,
////                    $this->panel . ' update failed.',
////                    [
////                        'data' => $request->all(),
////                    ]
////                );
////                $request->session()->flash('alert-danger', $this->panel . ' update failed!');
////            }
////        } catch (\Exception $exception) {
////            // Custom log for errors
////            logUserAction(
////                auth()->user()->id,
////                auth()->user()->team_id,
////                'Database Error during ' . $this->panel . ' update',
////                [
////                    'error' => $exception->getMessage(),
////                    'data' => $request->all(),
////                ]
////            );
////            $request->session()->flash('alert-danger', 'Database Error: ' . $exception->getMessage());
////        }
////
//
//        try {
//            $organization_id = $request->input('organization_id');
//            foreach ($request->input('gallery_category_id') as $index => $category_id) {
//                $type = $request->input('type')[$index];
//                if ($type == '1' && $request->hasFile('media_file')) {
//                    foreach ($request->file('media_file') as $fileIndex => $file) {
//                        $media_file = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
//                        $file->move(public_path('images/' . $this->folder . '/'), $media_file);
//                        $this->model::create([
//                            'gallery_category_id' => $category_id,
//                            'type' => $type,
//                            'caption' => $request->input('caption')[$index],
//                            'rank' => $request->input('rank')[$index],
//                            'media' => $media_file,
//                            'organization_id' => $organization_id,
//                            'updated_by' => auth()->user()->id,
//                        ]);
//                    }
//                }
//                if ($type == '0') {
//                    $this->model::create([
//                        'gallery_category_id' => $category_id,
//                        'type' => $type,
//                        'caption' => $request->input('caption')[$index],
//                        'rank' => $request->input('rank')[$index],
//                        'media' => $request->input('media')[$index],
//                        'organization_id' => $organization_id,
//                        'updated_by' => auth()->user()->id,
//                    ]);
//                }
//            }
//            return Utils\ResponseUtil::wrapResponse(new ResponseDTO(null, 'Gallery items uploaded uploads successfully.', 'success'));
//        } catch (\Exception $exception) {
//            Log::error('Error saving organization gallery data', ['error' => $exception->getMessage()]);
//            return Utils\ResponseUtil::wrapResponse(new ResponseDTO(null, 'An error occurred while saving gallery uploads items.', 'error'));
//        }
//    }

    public function update(OrganizationGalleryRequest $request, $id)
    {
        $data['record'] = $this->model->find($id);
        if (!$data['record']) {
            request()->session()->flash('failed', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }
        $request->request->add(['updated_by' => auth()->user()->id]);
        if ($request->hasfile('image_file')) {
            $existingDocuments = $data['record']->organizationGalleries()->get();
            foreach ($request->file('image_file') as $index => $images) {
                $fileName = time() . '_' . $images->getClientOriginalName();
                $images->move('images/' . $this->folder . '/', $fileName);

                if (isset($existingDocuments[$index])) {
                    if ($existingDocuments[$index]->image && file_exists(public_path('images/' . $this->folder . '/' . $existingDocuments[$index]->image))) {
                        unlink(public_path('images/' . $this->folder . '/' . $existingDocuments[$index]->image));
                    }
                    $existingDocuments[$index]->update([
                        'image_title' => $request->image_title[$index] ?? null,
                        'image_title_np' => $request->image_title_np[$index] ?? null,
                        'image' => $fileName,
                        'status' => 1,
                        'updated_by' => auth()->user()->id,

                    ]);
                } else {
                    $data['record']->organizationGalleries()->create([
                        'buddha_id ' => $data['record']->id,
                        'image_title' => $request->image_title[$index] ?? null,
                        'image_title_np' => $request->image_title_np[$index] ?? null,
                        'image' => $fileName,
                        'status' => 1,
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id,
                    ]);
                }
            }
        }
        try {
            $category = $data['record']->update($request->all());
            if ($category) {
                $request->session()->flash('success', $this->panel . ' updated successfully!');
            } else {
                $request->session()->flash('failed', $this->panel . ' update failed!');
            }
        } catch (\Exception $exception) {
            $request->session()->flash('failed', 'Database Error:' . $exception->getMessage());
        }
        return redirect()->route($this->base_route . 'index');
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
