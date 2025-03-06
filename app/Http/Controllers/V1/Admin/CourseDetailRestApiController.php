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


class CourseDetailRestApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/course/{id}",
     *     summary="Get course by ID",
     *     tags={"Course"},
     *     @OA\Parameter(
     *        name="id",
     *         in="path",
     *          description="ID of the course",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Course Name")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Course not found")
     * )
     */
    public function courseDetail(Request $request, $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'message' => 'Course not found!',
                'status' => 'error',
            ], 404);
        }
        $course->makeHidden([
             'rank','status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
             'meta_title', 'meta_keywords', 'meta_description','catalog_id','level_id','stream_id','university_id'
        ]);
        return response()->json([
            'message' => '',
            'status' => 'success',
            'data' => $course,
        ], 200);
    }

}
