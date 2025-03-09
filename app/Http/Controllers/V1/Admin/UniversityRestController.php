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
    /**
     * @OA\Get(
     *     path="/api/v1/university",
     *     summary="Get universities",
     *     tags={"University"},
     *     description="Retrieve a list of universities, with options for pagination.",
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items to get",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Number of items to skip",
     *         required=false,
     *         @OA\Schema(type="integer", example=0)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="University Name"),
     *                     @OA\Property(property="logo", type="string", example="http://example.com/file/university/logo.png")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="next_page_url", type="string", example="http://example.com/api/v1/university?limit=10&offset=10"),
     *                 @OA\Property(property="prev_page_url", type="string", example="http://example.com/api/v1/university?limit=10&offset=0")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No universities found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No universities found")
     *         )
     *     )
     * )
     */
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
                'rank', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
                 'country_id'
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
