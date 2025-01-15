<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdministrativeAreaRequest;
use App\Models\AdministrativeArea;
use App\Models\Level;
use App\Models\Stream;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class AdministrativeAreaController extends DM_BaseController
{
    protected $panel = 'AdministrativeArea';
    protected $base_route = 'admin.administrative_area';
    protected $view_path = 'admin.components.administrative_area';
    protected $model;
    protected $table;


    public function __construct(Request $request, AdministrativeArea $administrative_area)
    {
//        $this->middleware('auth');
//        $this->middleware('permission:administrative_area-list', ['only' => ['index']]);
//        $this->middleware('permission:administrative_area-create', ['only' => ['create', 'store']]);
//        $this->middleware('permission:administrative_area-show', ['only' => ['show']]);
//        $this->middleware('permission:administrative_area-edit', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:administrative_area-delete', ['only' => ['destroy']]);
//        $this->middleware('permission:administrative_area-restore', ['only' => ['restore']]);
//        $this->middleware('permission:administrative_area-forceDeleteData', ['only' => ['forceDeleteData']]);
        $this->model = $administrative_area;
    }
    /**
     * Display a listing of the resource.
     *@return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->model->with(['createds', 'parent'])->get();
            return response()->json($data);
        }
        $data['parents'] = AdministrativeArea::pluck('name' , 'id');
        return view(parent::loadView($this->view_path . '.index'), compact('data'));
    }
    // Fetch data for the DataTable
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->model->all();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editUrl = route($this->base_route . '.edit', ['id' => $row->id]);
                    $deleteUrl = route($this->base_route . '.destroy', ['id' => $row->id]);

                    $dropdown = '
                    <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <div class="dropdown-menu">
                            <button type="button"  class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editRecord(' . $row->id . ')">
                                <i class="bx bx-edit-alt me-1"></i> Edit
                          </button>
                            <form action="' . $deleteUrl . '" method="POST" onsubmit="return confirm(\'Are you sure?\');">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item">
                                    <i class="bx bx-trash me-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                ';
                    return $dropdown;
                })
                ->addColumn('parent', function ($row) {
                    return $row->parent->name ?? 'No Parent Id';
                })
                ->addColumn('createdBy', function ($row) {
                    return $row->createdBy->name ?? 'No';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d ');
                })
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     *
     */
    public function create(Request $request)
    {
        $data['parents'] = AdministrativeArea::pluck('name' , 'id');
        return view(parent::loadView($this->view_path . '.create'),compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function store(AdministrativeAreaRequest  $request)
    {
        //dd($request->all());
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
    public function edit($id)
    {
        try {
            $record = AdministrativeArea::find($id);
            if (!$record) {
                return response()->json(['error' => 'Record not found.'], 404);
            }

            $data['parents'] = AdministrativeArea::pluck('name', 'id');
            return response()->json(['record' => $record, 'parents' => $data['parents']]);
        } catch (\Exception $e) {
            Log::error('Error fetching record: ' . $e->getMessage());
            return response()->json(['error' => 'Server error. Please try again later.'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdministrativeAreaRequest  $request, $id)
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
