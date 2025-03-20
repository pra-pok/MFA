<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class StudentImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);
        Excel::import(new StudentsImport, $request->file('file'));
        return response()->json([
            'message' => 'Students imported successfully!'
        ], 200);
    }
}
