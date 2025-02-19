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
            $pagination = [
                'total' => $total,
                'limit' => (int) $limit,
                'offset' => (int) $offset,
                'next_offset' => $offset + $limit < $total ? $offset + $limit : null,
                'prev_offset' => $offset - $limit >= 0 ? $offset - $limit : null,
            ];
        } else {
            $universities = $query->paginate($perPage);
            $pagination = [
                'total' => $universities->total(),
                'per_page' => $universities->perPage(),
                'current_page' => $universities->currentPage(),
                'last_page' => $universities->lastPage(),
                'next_page_url' => $universities->nextPageUrl(),
                'prev_page_url' => $universities->previousPageUrl(),
            ];
        }
        $universities->each(function ($university) {
            $university->makeHidden([
                'id', 'rank', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
                'meta_title', 'meta_keywords', 'meta_description','country_id'
            ]);
            $university->logo = !empty($university->logo) ? url('/file/university/' . $university->logo) : '';
        });
        return Utils\ResponseUtil::wrapResponse(new ResponseDTO([
            'data' => $universities,
            'pagination' => $pagination
        ], '', 'success', 200));
    }


}
