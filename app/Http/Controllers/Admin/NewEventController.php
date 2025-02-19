<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\NewEventRequest;
use App\Models\NewEvent;
use App\Models\Organization;
use App\Models\OrganizationNewEvent;
use App\Models\Stream;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DM_BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class NewEventController extends DM_BaseController
{
    protected $panel = 'News Event';
    protected $base_route = 'admin.news_event';
    protected $view_path = 'admin.components.news_event';
    protected $model;
    protected $table;

    protected $folder = 'news_event';

    public function __construct(Request $request, NewEvent $news_event)
    {
        $this->model = $news_event;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->model->with(['createds' => function ($query) {
                $query->select('id', 'username');
            }, 'updatedBy' => function ($query) {
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
        return view(parent::loadView($this->view_path . '.create'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @return \Illuminate\Contracts\View\View
     */
    public function store(NewEventRequest $request)
    {
       // dd($request->all());
        $request->request->add(['created_by' => auth()->user()->id]);
        $request->request->add(['updated_by' => auth()->user()->id]);
        if ($request->hasfile('thumbnail_file')) {
            $fileDirectory = '/data/mfa/images/' . $this->folder . '/';
            if (!file_exists($fileDirectory)) {
                mkdir($fileDirectory, 0777, true);
            }
            $thumbnail_file = time() . '.' . $request->file('thumbnail_file')->getClientOriginalExtension();
            $request->file('thumbnail_file')->move($fileDirectory, $thumbnail_file);
            $request->request->add(['thumbnail' => $thumbnail_file]);
        }
        if ($request->hasfile('pdf_file')) {
            $fileDirectory = '/data/mfa/file/' . $this->folder . '/';
            if (!file_exists($fileDirectory)) {
                mkdir($fileDirectory, 0777, true);
            }
            $pdf_file = time() . '.' . $request->file('pdf_file')->getClientOriginalExtension();
            $request->file('pdf_file')->move($fileDirectory, $pdf_file);
            $request->request->add(['file' => $pdf_file]);
        }
        try {
            $category = $this->model->create($request->all());
            if ($request->has('organization_id')) {
                $newevents = $request->organization_id;
                $newEvents = [];
                foreach ($newevents as $eventId) {
                    $newEvents[] = [
                        'new_event_id' => $category->id,
                        'organization_id' => $eventId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                OrganizationNewEvent::insert($newEvents);
            }
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
     * @param int $id
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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\View\View
     */
    public function edit($id): \Illuminate\Http\Response|\Illuminate\Contracts\View\View
    {
        $data['record'] = $this->model->with('organizations')->find($id);
        if (!$data['record']) {
            request()->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . 'index');
        }
        return view(parent::loadView($this->view_path . '.edit'), compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(NewEventRequest $request, $id)
    {
        $data['record'] = $this->model->find($id);
        if (!$data['record']) {
            $request->session()->flash('alert-danger', 'Invalid Request');
            return redirect()->route($this->base_route . '.index');
        }
        $request->request->add(['updated_by' => auth()->user()->id]);
        if ($request->hasfile('thumbnail_file')) {
            $fileDirectory = '/data/mfa/images/' . $this->folder . '/';
            if (!file_exists($fileDirectory)) {
                mkdir($fileDirectory, 0777, true);
            }
            $thumbnail_file = time() . '.' . $request->file('thumbnail_file')->getClientOriginalExtension();

            $request->file('thumbnail_file')->move($fileDirectory, $thumbnail_file);
            $request->request->add(['thumbnail' => $thumbnail_file]);
            if ($data['record']->thumbnail && file_exists(public_path($fileDirectory . $data['record']->thumbnail))) {
                unlink(public_path($fileDirectory . $data['record']->thumbnail));
            }
        } else {
            $request->request->add(['thumbnail' => $data['record']->thumbnail]);
        }
        if ($request->hasfile('pdf_file')) {
            $fileDirectory = '/data/mfa/file/' . $this->folder . '/';
            if (!file_exists($fileDirectory)) {
                mkdir($fileDirectory, 0777, true);
            }
            $pdf_file = time() . '.' . $request->file('pdf_file')->getClientOriginalExtension();

            $request->file('pdf_file')->move($fileDirectory, $pdf_file);
            $request->request->add(['file' => $pdf_file]);
            if ($data['record']->file && file_exists(public_path($fileDirectory . $data['record']->file))) {
                unlink(public_path($fileDirectory . $data['record']->file));
            }
        } else {
            $request->request->add(['file' => $data['record']->file]);
        }
        try {
            $category = $data['record']->update($request->all());
            if ($category) {
                if ($request->has('organization_id')) {
                    $newevents = $request->organization_id;
                    $newEvents = [];
                    foreach ($newevents as $eventId) {
                        $newEvents[] = [
                            'new_event_id' => $data['record']->id,  // Using $data['record']->id here
                            'organization_id' => $eventId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    // Delete old associations and insert new ones
                    OrganizationNewEvent::where('new_event_id', $data['record']->id)->delete();
                    OrganizationNewEvent::insert($newEvents);
                }
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
     * @param int $id
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
    public function search(Request $request)
    {
        $query = $request->q;
        $organizations = Organization::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name')
            ->limit(1)
            ->get();
        return response()->json($organizations);
    }
}
