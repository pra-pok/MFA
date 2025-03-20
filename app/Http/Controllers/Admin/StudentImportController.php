<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\Student;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentImportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Student::orderBy('created_at', 'desc')->get();
            return response()->json($data);
        }
        return view('admin.components.students.index');
    }
    public function create(Request $request)
    {
        return view('admin.components.students.create');
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,csv,xls'
            ]);
            Excel::import(new StudentsImport, $request->file('file'));
            logUserAction(
                auth()->id(),
                auth()->user()->team_id,
                "Student data imported successfully!",
                ['file' => $request->file('file')->getClientOriginalName()]
            );
            $request->session()->flash('alert-success', 'Student data imported successfully!');
        } catch (\Exception $exception) {
            logUserAction(
                auth()->id(),
                auth()->user()->team_id,
                'Error during student data import',
                [
                    'error' => $exception->getMessage(),
                    'file'  => $request->file('file')->getClientOriginalName() ?? 'N/A',
                ]
            );
            $request->session()->flash('alert-danger', "Import failed: {$exception->getMessage()}");
        }
        return redirect()->route("{$this->base_route}.index");
    }
}
