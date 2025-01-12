<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UniversityRequest;
use App\Models\University;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class UniversityController extends DM_BaseController
{
    protected $panel = 'University';
    protected $base_route = 'admin.university';
    protected $view_path = 'admin.components.university';
    protected $model;
    protected $table;



    public function __construct(Request $request, University $university)
    {
//        $this->middleware('auth');
//        $this->middleware('permission:university-list', ['only' => ['index']]);
//        $this->middleware('permission:university-create', ['only' => ['create', 'store']]);
//        $this->middleware('permission:university-show', ['only' => ['show']]);
//        $this->middleware('permission:university-edit', ['only' => ['edit', 'update']]);
//        $this->middleware('permission:university-delete', ['only' => ['destroy']]);
//        $this->middleware('permission:university-restore', ['only' => ['restore']]);
//        $this->middleware('permission:university-forceDeleteData', ['only' => ['forceDeleteData']]);
        $this->model = $university;
    }
    /**
     * Display a listing of the resource.
     *@return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data['rows'] = $this->model->all();
        return view(parent::loadView($this->view_path . '.index'), compact('data'));
    }
    public function getData1()
    {
        $rows = $this->model->all(); // Fetch all data
        return response()->json(['data' => $rows]);
    }
    // Fetch data for the DataTable
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->model->all();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $editButton = '<a href="/admin/university/' . $row->id . '/edit" class="btn rounded-pill btn-warning">
                                <i class="icon-base bx bx-edit icon-sm"></i>
                               </a>';
                    $showButton = '<a href="/admin/university/' . $row->id . '/show" class="btn rounded-pill btn-info">
                                <i class="bx bx-show"></i>
                               </a>';
                    $deleteButton = '<form action="/admin/university/' . $row->id . '" class="d-inline" method="post" onsubmit="return confirm(\'Are you sure to delete?\')">
                                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn rounded-pill btn-danger" title="Move to Trash">
                                        <i class="bx bx-trash me-1"></i>
                                    </button>
                                 </form>';


                    return $editButton . ' ' . $showButton . ' ' . $deleteButton;
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
        $data['type'] = ['Affiliated' => 'Affiliated', 'Foreign Affiliated' => 'Foreign Affiliated'];
        return view(parent::loadView($this->view_path . '.create'),compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function store(UniversityRequest $request)
    {
       // dd($request->all());
        $request->request->add(['created_by' => auth()->user()->id]);
        if ($request->hasfile('image_file')) {
            $image_file = time() . '.' . $request->file('image_file')->getClientOriginalExtension();
            $request->file('image_file')->move('images/' . $this->folder . '/', $image_file);
            $request->request->add(['logo' => $image_file]);
        }
            try {
                $category = $this->model->create($request->all());
                if ($category) {
                    Log::info($this->panel . ' created successfully!', ['user_id' => auth()->user()->name, 'data' => $request->all()]);
                    $request->session()->flash('alert-success', $this->panel . ' created successfully!');
                } else {
                    $request->session()->flash('alert-danger', $this->panel . ' creation failed!');
                }
            } catch (\Exception $exception) {
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
    public function edit($id): \Illuminate\Http\Response|\Illuminate\Contracts\View\View
    {
        $data['type'] = ['Affiliated' => 'Affiliated', 'Foreign Affiliated' => 'Foreign Affiliated'];
        $data['record'] = $this->model->find($id);
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
    public function update(UniversityRequest $request, $id)
    {
       // dd($request->all());
        $data['record'] = $this->model->find($id);
        if (!$data['record']) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }
        if ($request->hasfile('image_file')) {
            $image_file = time() . '.' . $request->file('image_file')->getClientOriginalExtension();

            $request->file('image_file')->move('images/' . $this->folder . '/', $image_file);
            $request->request->add(['logo' => $image_file]);
            if ($data['record']->logo && file_exists(public_path('images/' . $this->folder . '/' . $data['record']->logo))) {
                unlink(public_path('images/' . $this->folder . '/' . $data['record']->logo));
            }
        } else {
            $request->request->add(['logo' => $data['record']->logo]);
        }
        // dd($request);
        $request->request->add(['updated_by' => auth()->user()->id]);
        try {
            $category = $data['record']->update($request->all());
            if ($category) {
                Log::channel('daily')->info($this->panel . ' updated successfully!', ['user_id' => auth()->user()->name, 'data' => $request->all()]);
                $request->session()->flash('alert-success', $this->panel . ' updated successfully!');
            } else {
                $request->session()->flash('alert-danger', $this->panel . ' update failed!');
            }
        } catch (\Exception $exception) {
            Log::error('Database Error', ['error' => $exception->getMessage()]);
            $request->session()->flash('alert-danger', 'Database Error:' . $exception->getMessage());
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
            Log::info($this->panel . ' moved to trash successfully!', ['user_id' => auth()->user()->name, 'data' => request()->all()]);
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
            Log::info($this->panel . ' restore successfully!', ['user_id' => auth()->user()->name, 'data' => request()->all()]);
            request()->session()->flash('alert-success', $this->panel . ' restore successfully!');
        } else {
            request()->session()->flash('alert-danger', $this->panel . ' restore failed!');
        }
        return redirect()->route($this->base_route . '.index');
    }

    public function forceDeleteData($id)
    {
        $record = $this->model->where('id', $id)->withTrashed()->first();
        // dd('images/' . $this->folder . '/' .$record->image);
        $banner = public_path('images/' . $this->folder . '/' . $record->image);
        // dd(File::exists($banner));
        if (File::exists($banner)) {
            File::delete($banner);
        }
        if (!$record) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . '.index');
        }
        if ($record->forceDelete()) {
            Log::info($this->panel . ' deleted successfully!', ['user_id' => auth()->user()->name, 'data' => request()->all()]);
            request()->session()->flash('alert-success', $this->panel . ' deleted successfully!');
        } else {
            request()->session()->flash('alert-danger', $this->panel . ' delete failed!');
        }
        return redirect()->route($this->base_route . '.index');
    }

}
