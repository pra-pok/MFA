<?php

namespace App\Http\Controllers\Admin;

use App\Dtos\ResponseDTO;
use App\Http\Requests\MenuRequest;
use App\Models\AdministrativeArea;
use App\Models\Country;
use App\Models\Menu;
use App\Utils;
use Illuminate\Http\Request;

class MenuController extends DM_BaseController
{
    protected $panel = 'Menu';
    protected $base_route = 'menu';
    protected $view_path = 'admin.components.menu';
    protected $model;
    protected $table;


    public function __construct(Request $request, Menu $menu)
    {
        $this->model = $menu;
    }
    /**
     * Display a listing of the resource.
     *@return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Menu::with(['createds:id,username', 'updatedBy:id,username', 'parent:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();
            return response()->json(['data' => $data]);
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
       $data['role'] = ['Module' => 'Module', 'Sub Module' => 'Sub Module', 'Menuline' => 'Menuline', 'Sub Menuline' => 'Sub Menuline'];
        $data['parents'] = Menu::whereNull('parent_id')->pluck('name', 'id');
        return view(parent::loadView($this->view_path . '.create'),compact('data'));
    }

    public function getAllData($role_id)
    {
        $modules = Menu::whereNull('parent_id')->get(['id', 'name']);
        $subModules = Menu::whereIn('parent_id', $modules->pluck('id'))->get(['id', 'name', 'parent_id']);
        $menuLines = Menu::whereIn('parent_id', $subModules->pluck('id'))->get(['id', 'name', 'parent_id']);

        return response()->json([
            'modules' => $modules,
            'subModules' => $subModules,
            'menuLines' => $menuLines
        ]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function store(MenuRequest  $request)
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
            return redirect()->route($this->base_route . '.index');
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
        $data['record'] = $this->model::with('directParent' ,'directParent.grandParent',
            'directParent.grandParent.greatGrandParent')->find($id);
        if (!$data['record']) {
            return redirect()->route($this->base_route . '.index')->with('alert-danger', 'Invalid Request');
        }
        $data['role'] = ['Module' => 'Module', 'Sub Module' => 'Sub Module', 'Menuline' => 'Menuline', 'Sub Menuline' => 'Sub Menuline'];
        $data['parents'] = Menu::whereNull('parent_id')->pluck('name', 'id');

        if ($data['record']->role === 'Module') {
            $data['moduleDataId'] = $data['record']->directParent->id ?? '';
            $data['subModuleDataId'] = '';
            $data['menulineDataId'] = '';
        } elseif ($data['record']->role === 'Sub Module') {
            $data['moduleDataId'] = $data['record']->directParent->grandParent->id ?? '';
            $data['subModuleDataId'] = $data['record']->directParent->id ?? '';
            $data['menulineDataId'] = '';
        } elseif ($data['record']->role === 'Menuline') {
            $data['moduleDataId'] = $data['record']->directParent->grandParent->id ?? '';
            $data['subModuleDataId'] = $data['record']->directParent->id ?? '';
            $data['menulineDataId'] = $data['record']->directParent->id ?? '';
        } elseif ($data['record']->role === 'Sub Menuline') {
            $data['moduleDataId'] = $data['record']->directParent->grandParent->greatGrandParent->id ?? '';
            $data['subModuleDataId'] = $data['record']->directParent->grandParent->id ?? '';
            $data['menulineDataId'] = $data['record']->directParent->id ?? '';
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
    public function update(MenuRequest  $request, $id)
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
