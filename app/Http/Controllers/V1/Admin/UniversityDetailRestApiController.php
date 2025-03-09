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
     *     description="Retrieve the details of a university by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the university",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="University Name"),
     *                 @OA\Property(property="logo", type="string", example="http://example.com/file/university/logo.png")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="University not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="University not found!")
     *         )
     *     )
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
            'catalog_id', 'country_id'
        ]);

        return response()->json([
            'message' => '',
            'status' => 'success',
            'data' => $university,
        ], 200);
    }

}
