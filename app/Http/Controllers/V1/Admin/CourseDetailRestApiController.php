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

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="My API",
 *      description="API documentation",
 *      @OA\Contact(
 *          email="support@example.com"
 *      )
 * )
 */
class CourseDetailRestApiController extends Controller
{
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
