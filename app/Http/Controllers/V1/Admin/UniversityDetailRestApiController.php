<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;
use OpenApi\Annotations as OA;
class UniversityDetailRestApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/university/{id}",
     *     summary="Get university by ID",
     *     tags={"University"},
     *    @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="ID of the course",
     *      required=true,
     *      @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="University Name")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="University not found")
     * )
     */
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
