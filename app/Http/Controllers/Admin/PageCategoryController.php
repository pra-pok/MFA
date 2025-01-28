<?php

namespace App\Http\Controllers\Admin;

use App\Dtos\ResponseDTO;
use App\Http\Requests\PageCategoryRequest;
use App\Models\PageCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\Log;
use App\Utils;
class PageCategoryController extends DM_BaseController
{
    protected $panel = 'Page Category';
    protected $base_route = 'page-category';
    protected $view_path = 'admin.components.page_category';
    protected $model;
    protected $table;



    public function __construct(Request $request, PageCategory $page_category)
    {
        $this->model = $page_category;
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
        return view(parent::loadView($this->view_path . '.create'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function store(PageCategoryRequest  $request)
    {
       // dd($request->all());
        $request->request->add(['created_by' => auth()->user()->id]);

            try {
                $category = $this->model->create($request->all());
                if ($category) {
                    logUserAction(
                        auth()->user()->id, // User ID
                        auth()->user()->team_id, // Team ID
                        $this->panel . ' created successfully!',
                        [
                            'data' => $request->all(),
                        ]
                    );

                    $request->session()->flash('alert-success', $this->panel . ' created successfully!');
                } else {
                    logUserAction(
                        auth()->user()->id,
                        auth()->user()->team_id,
                        $this->panel . ' creation failed.',
                        [
                            'data' => $request->all(),
                        ]
                    );
                    $request->session()->flash('alert-danger', $this->panel . ' creation failed!');
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
                Log::error('Database Error', ['error' => $exception->getMessage()]);
                $request->session()->flash('alert-danger', 'Database Error: ' . $exception->getMessage());
            }
        return redirect()->route($this->base_route . '.index');
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
//    public function edit($id): \Illuminate\Http\Response|\Illuminate\Contracts\View\View
//    {
//        $data['record'] = $this->model->find($id);
//        if (!$data['record']) {
//            request()->session()->flash('alert-danger', 'Invalid Request');
//            return redirect()->route($this->base_route . 'index');
//        }
//
//        return view(parent::loadView($this->view_path . '.edit'), compact('data'));
//    }
    public function edit($id)
    {
        $data['record'] = $this->model->find($id);
        if (!$data['record']) {
            return redirect()->route($this->base_route . '.index')->with('alert-danger', 'Invalid Request');
        }

        if (request()->ajax()) {
            return Utils\ResponseUtil::wrapResponse(new ResponseDTO($data['record'], 'Record fetched successfully.', 'success'));
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
    public function update(PageCategoryRequest  $request, $id)
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
