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

class CourseRestController extends Controller
{
    public function getCourse(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $limit = $request->input('limit');
        $offset = $request->input('offset', 0);
        $query = Course::with(['stream', 'level'])->where('status', 1)->orderBy('id', 'desc');
        if ($limit) {
            $total = $query->count();
            $courses = $query->limit($limit)->offset($offset)->get();
            $pagination = [
                'total' => $total,
                'limit' => (int) $limit,
                'offset' => (int) $offset,
                'next_offset' => $offset + $limit < $total ? $offset + $limit : null,
                'prev_offset' => $offset - $limit >= 0 ? $offset - $limit : null,
            ];
        } else {
            $courses = $query->paginate($perPage);
            $pagination = [
                'total' => $courses->total(),
                'per_page' => $courses->perPage(),
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'next_page_url' => $courses->nextPageUrl(),
                'prev_page_url' => $courses->previousPageUrl(),
            ];
        }
        $courses->each(function ($course) {
            $course->makeHidden([
                'id', 'rank', 'stream_id', 'level_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
                'meta_title', 'meta_keywords', 'meta_description',
            ]);
            foreach (['stream', 'level'] as $relation) {
                if ($course->$relation) {
                    $course->$relation->makeHidden([
                        'id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
                        'meta_title', 'meta_keywords', 'meta_description', 'rank'
                    ]);
                }
            }
        });
        return Utils\ResponseUtil::wrapResponse(new ResponseDTO([
            'data' => $courses,
            'pagination' => $pagination
        ], '', 'success', 200));
    }


}
