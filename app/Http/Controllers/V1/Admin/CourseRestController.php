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
     *     path="/api/v1/course",
     *     summary="Get courses",
     *     tags={"Course"},
     *     description="Retrieve a list of courses with optional pagination parameters.",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (for pagination)",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, example=10)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items to retrieve",
     *         required=false,
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Number of items to skip (used with limit)",
     *         required=false,
     *         @OA\Schema(type="integer", example=0)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-03-03T12:00:00Z"),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="https://example.com/api/v1/course?limit=5&offset=5"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example="https://example.com/api/v1/course?limit=5&offset=0")
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Computer Science")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No courses found"
     *     )
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
                'eligibility', 'job_prospects', 'syllabus'
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
