<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Course;
use App\Models\Level;
use App\Models\NewEvent;
use App\Models\Organization;
use App\Models\Review;
use App\Models\Stream;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;
use OpenApi\Annotations as OA;

class UniversityRestController extends Controller
{
    public function getUniversity(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $limit = $request->input('limit');
        $offset = $request->input('offset', 0);
        $query = University::where('status', 1)->orderBy('id', 'desc');
        if ($limit) {
            $total = $query->count();
            $universities = $query->limit($limit)->offset($offset)->get();
            $meta = [
                'total' => $total,
                'per_page' => (int) $limit,
                'current_page' => (int) ceil(($offset + 1) / $limit),
                'last_page' => (int) ceil($total / $limit),
                'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
            ];
        } else {
            $paginatedUniversities = $query->paginate($perPage);
            $meta = [
                'total' => $paginatedUniversities->total(),
                'per_page' => $paginatedUniversities->perPage(),
                'current_page' => $paginatedUniversities->currentPage(),
                'last_page' => $paginatedUniversities->lastPage(),
                'next_page_url' => $paginatedUniversities->nextPageUrl(),
                'prev_page_url' => $paginatedUniversities->previousPageUrl(),
            ];
            $universities = collect($paginatedUniversities->items());
        }
        $universities->each(function ($university) {
            $university->makeHidden([
                'id', 'rank', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
                'meta_title', 'meta_keywords', 'meta_description', 'country_id'
            ]);
            $university->logo = !empty($university->logo) ? url('/file/university/' . $university->logo) : '';
        });
        return response()->json([
            'data' => $universities,
            'meta' => $meta,
            'message' => '',
            'status' => true,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
