<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
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
class UniversityDetailRestApiController extends Controller
{
    public function universityDetail(Request $request, $id)
    {
        $university = University::find($id);
        if (!$university) {
            return response()->json([
                'message' => 'University not found!',
                'status' => 'error',
            ], 404);
        }

        $university->logo = !empty($university->logo) ? url('/file/university/' . $university->logo) : '';
        $university->makeHidden([
            'rank', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
            'meta_title', 'meta_keywords', 'meta_description', 'catalog_id', 'country_id'
        ]);

        return response()->json([
            'message' => '',
            'status' => 'success',
            'data' => $university,
        ], 200);
    }

}
