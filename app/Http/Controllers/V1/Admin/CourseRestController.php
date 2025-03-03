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
    /**
     * @OA\Get(
     *     path="/course",
     *     summary="Get courses",
     *     tags={"Course"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items to get",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Number of items to skip",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Course Name")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=404, description="No courses found")
     * )
     */
    public function getCourse(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $limit = $request->input('limit');
        $offset = $request->input('offset', 0);
        $query = Course::where('status', 1)->orderBy('id', 'desc');
        if ($limit) {
            $total = $query->count();
            $courses = $query->limit($limit)->offset($offset)->get();
            $meta = [
                'total' => $total,
                'per_page' => (int) $limit,
                'current_page' => (int) ceil(($offset + 1) / $limit),
                'last_page' => (int) ceil($total / $limit),
                'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
            ];
        } else {
            $paginatedCourses = $query->paginate($perPage);
            $meta = [
                'total' => $paginatedCourses->total(),
                'per_page' => $paginatedCourses->perPage(),
                'current_page' => $paginatedCourses->currentPage(),
                'last_page' => $paginatedCourses->lastPage(),
                'next_page_url' => $paginatedCourses->nextPageUrl(),
                'prev_page_url' => $paginatedCourses->previousPageUrl(),
            ];
            $courses = collect($paginatedCourses->items());
        }
        $courses->each(function ($course) {
            $course->makeHidden([
                'rank', 'stream_id', 'level_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
                'meta_title', 'meta_keywords', 'meta_description', 'eligibility', 'job_prospects', 'syllabus'
            ]);
        });

        return response()->json([
            'data' => $courses,
            'meta' => $meta,
            'message' => '',
            'status' => true,
            'timestamp' => now()->toISOString(),
        ]);
    }
}
