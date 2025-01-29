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
//    public function store(Request $request)
//    {
//        try {
//            $organization_id = $request->input('organization_id');
//            $gallery_category_ids = $request->input('gallery_category_id');
//            $captions = $request->input('caption');
//            $ranks = $request->input('rank');
//            $created_by = auth()->id();
//            $updated_by = auth()->id();
//
//            foreach ($gallery_category_ids as $index => $category_id) {
//                $galleryItem = OrganizationGallery::where('organization_id', $organization_id)
//                    ->where('gallery_category_id', $category_id)
//                    ->first();
//
//                // Handle new or existing records
//                if ($galleryItem) {
//                    // Update existing item caption and rank
//                    $galleryItem->caption = $captions[$index];
//                    $galleryItem->rank = $ranks[$index];
//                    $galleryItem->updated_by = $updated_by;
//                  //  $galleryItem->save();
//                    // Handle image uploads
//                    if ($request->hasFile("media_file.$index")) {
//                        foreach ($request->file("media_file.$index") as $file) {
//                            $media_file_name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
//                            $file->move(public_path("images/organization-gallery/"), $media_file_name);
//                            // Create new image records
//                            OrganizationGallery::create([
//                                'organization_id' => $organization_id,
//                                'gallery_category_id' => $category_id,
//                                'caption' => $captions[$index],
//                                'rank' => $ranks[$index],
//                                'type' => 1, // Image type
//                                'media' => $media_file_name,
//                                'created_by' => $created_by,
//                                'updated_by' => $updated_by,
//                            ]);
//                        }
//                    }
//                    // Handle video URL for updating records
//                    if (!empty($request->input("media.$index"))) {
//                        $galleryItem->type = 0; // Video type
//                        $galleryItem->media = $request->input("media.$index");
//                        $galleryItem->save();
//                    }
//                } else {
//                    if ($request->hasFile("media_file.$index")) {
//                        foreach ($request->file("media_file.$index") as $file) {
//                            $media_file_name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
//                            $file->move(public_path("images/organization-gallery/"), $media_file_name);
//                            OrganizationGallery::create([
//                                'organization_id' => $organization_id,
//                                'gallery_category_id' => $category_id,
//                                'caption' => $captions[$index],
//                                'rank' => $ranks[$index],
//                                'type' => 1, // Image type
//                                'media' => $media_file_name,
//                                'created_by' => $created_by,
//                                'updated_by' => $updated_by,
//                            ]);
//                        }
//                    }
//                    // Handle video URL for new records
//                    if (!empty($request->input("media.$index"))) {
//                        OrganizationGallery::create([
//                            'organization_id' => $organization_id,
//                            'gallery_category_id' => $category_id,
//                            'caption' => $captions[$index],
//                            'rank' => $ranks[$index],
//                            'type' => 0, // Video type
//                            'media' => $request->input("media.$index"),
//                            'created_by' => $created_by,
//                            'updated_by' => $updated_by,
//                        ]);
//                    }
//                }
//            }
//            return Utils\ResponseUtil::wrapResponse(
//                new ResponseDTO(null, 'Gallery items stored/updated successfully.', 'success')
//            );
//        } catch (\Exception $exception) {
//            Log::error('Error saving/updating gallery data', [
//                'error' => $exception->getMessage(),
//            ]);
//
//            return Utils\ResponseUtil::wrapResponse(
//                new ResponseDTO(null, 'An error occurred while saving/updating gallery items.', 'error')
//            );
//        }
//    }
    public function store(Request $request)
    {
        try {
            $organization_id = $request->input('organization_id');
            $gallery_category_ids = $request->input('gallery_category_id');
            $captions = $request->input('caption');
            $ranks = $request->input('rank');
            $created_by = auth()->id();
            $updated_by = auth()->id();

            foreach ($gallery_category_ids as $index => $category_id) {
                // Check if this gallery item exists
                $galleryItem = OrganizationGallery::where('organization_id', $organization_id)
                    ->where('gallery_category_id', $category_id)
                    ->first();

                // Handle new or existing records
                if ($galleryItem) {
                    // Update existing item caption and rank
                    $galleryItem->caption = $captions[$index];
                    $galleryItem->rank = $ranks[$index];
                    $galleryItem->updated_by = $updated_by;

                    // Handle image uploads
                    if ($request->hasFile("media_file.$index")) {
                        foreach ($request->file("media_file.$index") as $file) {
                            $media_file_name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path("images/organization-gallery/"), $media_file_name);

                            // Create new image records
                            OrganizationGallery::create([
                                'organization_id' => $organization_id,
                                'gallery_category_id' => $category_id,
                                'caption' => $captions[$index],
                                'rank' => $ranks[$index],
                                'type' => 1, // Image type
                                'media' => $media_file_name,
                                'created_by' => $created_by,
                                'updated_by' => $updated_by,
                            ]);
                        }
                    }

                    // Handle video URL for updating records
                    if (!empty($request->input("media.$index"))) {
                        $galleryItem->type = 0; // Video type
                        $galleryItem->media = $request->input("media.$index");
                        $galleryItem->save();
                    }

                } else {
                    // Handle new item creation
                    // Handle image uploads
                    if ($request->hasFile("media_file.$index")) {
                        foreach ($request->file("media_file.$index") as $file) {
                            $media_file_name = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path("images/organization-gallery/"), $media_file_name);

                            // Create new image records
                            OrganizationGallery::create([
                                'organization_id' => $organization_id,
                                'gallery_category_id' => $category_id,
                                'caption' => $captions[$index],
                                'rank' => $ranks[$index],
                                'type' => 1, // Image type
                                'media' => $media_file_name,
                                'created_by' => $created_by,
                                'updated_by' => $updated_by,
                            ]);
                        }
                    }

                    // Handle video URL for new records
                    if (!empty($request->input("media.$index"))) {
                        OrganizationGallery::create([
                            'organization_id' => $organization_id,
                            'gallery_category_id' => $category_id,
                            'caption' => $captions[$index],
                            'rank' => $ranks[$index],
                            'type' => 0, // Video type
                            'media' => $request->input("media.$index"),
                            'created_by' => $created_by,
                            'updated_by' => $updated_by,
                        ]);
                    }
                }
            }

            return Utils\ResponseUtil::wrapResponse(
                new ResponseDTO(null, 'Gallery items stored/updated successfully.', 'success')
            );
        } catch (\Exception $exception) {
            Log::error('Error saving/updating gallery data', [
                'error' => $exception->getMessage(),
            ]);

            return Utils\ResponseUtil::wrapResponse(
                new ResponseDTO(null, 'An error occurred while saving/updating gallery items.', 'error')
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
        $data['gallery'] = GalleryCategory::pluck('name', 'id');
        $data['organization'] = Organization::pluck('name', 'id');
        $data['type'] = ['Video' => 'Video', 'Image' => 'Image'];
        $data['record'] = $this->model->find($id);
       // dd($data['record']);
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

    public function update(OrganizationGalleryRequest $request, $organization_id)
    {
        $data['record'] = $this->model->find($organization_id);
        dd($data['record']);
        if (!$data['record']) {
            return Utils\ResponseUtil::wrapResponse(
                new ResponseDTO(null, 'Invalid Request', 'error')
            );
        }
        $request->request->add(['updated_by' => auth()->user()->id]);
//        $organization_id = $data['record']->organization_id;
        $existingDocuments = $data['record']->organizationGalleries()->get()->keyBy('id');
        foreach ($request->input('gallery_category_id', []) as $index => $category_id) {
            $type = $request->input('type')[$index];
            $caption = $request->input('caption')[$index] ?? null;
            $rank = $request->input('rank')[$index] ?? null;
            $media_text = $request->input('media')[$index] ?? null;
            $existingDocument = $existingDocuments->get($index);
            // Handle Image
            if ($type == '1' && $request->hasFile("media_file.$index")) {
                $file = $request->file("media_file.$index");
                $media_file = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/' . $this->folder . '/'), $media_file);

                if ($existingDocument) {
                    // Delete old file if exists
                    if ($existingDocument->media && file_exists(public_path('images/' . $this->folder . '/' . $existingDocument->media))) {
                        unlink(public_path('images/' . $this->folder . '/' . $existingDocument->media));
                    }
                }
            } else {
                $media_file = $existingDocument->media ?? null;
            }
            // Update or Create
            if ($existingDocument) {
                $existingDocument->update([
                    'gallery_category_id' => $category_id,
                    'type' => $type,
                    'caption' => $caption,
                    'rank' => $rank,
                    'media' => $type == '1' ? $media_file : $media_text,
//                    'organization_id' => $organization_id,
                    'updated_by' => auth()->user()->id,
                ]);
            } else {
                $data['record']->organizationGalleries()->create([
                    'gallery_category_id' => $category_id,
                    'type' => $type,
                    'caption' => $caption,
                    'rank' => $rank,
                    'media' => $type == '1' ? $media_file : $media_text,
                    'organization_id' => $organization_id,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);
            }
        }
        try {
            $data = $data['record']->update($request->all());
            if ($data) {
                return Utils\ResponseUtil::wrapResponse(
                    new ResponseDTO($data, 'Gallery items updated successfully.', 'success')
                );
            } else {
                return Utils\ResponseUtil::wrapResponse(
                    new ResponseDTO(null, 'No changes made to the gallery.', 'error')
                );
            }
        } catch (\Exception $exception) {
            Log::error('Error updating organization gallery data', ['error' => $exception->getMessage()]);
            return Utils\ResponseUtil::wrapResponse(
                new ResponseDTO($data, 'An error occurred while updating gallery items.', 'error')
            );
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

    public function permanentDelete($id)
    {
        $record = OrganizationGallery::find($id);

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
