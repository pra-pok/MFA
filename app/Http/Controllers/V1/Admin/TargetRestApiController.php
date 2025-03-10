<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\CounselorReferrer;
use App\Models\Target;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class TargetRestApiController extends Controller
{
    /**
     * Get List of Target with Pagination & Limit
     * @OA\Get (
     *     path="/api/v1/target",
     *     security={{"Bearer": {}}},
     *     tags={"Target"},
     *     summary="Retrieve a list of Target with pagination",
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
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="counselor_referrer_id", type="integer", example=1),
     *                     @OA\Property(property="academic_year_id", type="integer", example=1),
     *                     @OA\Property(property="min_target", type="integer", example=10),
     *                     @OA\Property(property="max_target", type="integer", example=20),
     *                     @OA\Property(property="amount_percentage", type="string", example="10%"),
     *                     @OA\Property(property="type", type="string", example="type"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T12:00:00.000000Z")
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="http://example.com/api/v1/academic/year?per_page=10&page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(property="message", type="string", example="Target retrieved successfully"),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-03-10T12:00:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No Target Data found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No Target Data found"),
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $limit = $request->input('limit');
            $offset = $request->input('offset', 0);
            $query = Target::query()->orderBy('id', 'desc');
            if ($limit) {
                $total = $query->count();
                $target = $query->limit($limit)->offset($offset)->get();
                $meta = [
                    'total' => $total,
                    'per_page' => (int) $limit,
                    'current_page' => (int) ceil(($offset + 1) / $limit),
                    'last_page' => (int) ceil($total / $limit),
                    'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                    'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
                ];
            } else {
                $paginatedTarget = $query->paginate($perPage);
                $meta = [
                    'total' => $paginatedTarget->total(),
                    'per_page' => $paginatedTarget->perPage(),
                    'current_page' => $paginatedTarget->currentPage(),
                    'last_page' => $paginatedTarget->lastPage(),
                    'next_page_url' => $paginatedTarget->nextPageUrl(),
                    'prev_page_url' => $paginatedTarget->previousPageUrl(),
                ];
                $target = collect($paginatedTarget->items());
            }
            return response()->json([
                'data' => $target,
                'meta' => $meta,
                'message' => '',
                'status' => true,
                'timestamp' => now()->toISOString(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Store multiple Targets
     * @OA\Post (
     *     path="/api/v1/target",
     *     security={{"Bearer": {}}},
     *     tags={"Target"},
     *     summary="Create multiple Targets",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"academic_year_id", "targets"},
     *                 @OA\Property(property="counselor_referrer_id", type="integer", nullable=true, example=null),
     *                 @OA\Property(property="academic_year_id", type="integer", example=2),
     *                 @OA\Property(
     *                     property="targets",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="min_target", type="integer", example=10),
     *                         @OA\Property(property="max_target", type="integer", example=20),
     *                         @OA\Property(property="amount_percentage", type="string", example="10%"),
     *                         @OA\Property(property="type", type="string", example="amount")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Targets added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="counselor_referrer_id", type="integer", example=1),
     *                     @OA\Property(property="academic_year_id", type="integer", example=2),
     *                     @OA\Property(property="min_target", type="integer", example=10),
     *                     @OA\Property(property="max_target", type="integer", example=20),
     *                     @OA\Property(property="amount_percentage", type="string", example="10%"),
     *                     @OA\Property(property="type", type="string", example="amount"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-10T10:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-10T10:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *             @OA\Property(property="error", type="string", example="Unexpected error occurred")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'counselor_referrer_id' => [
                'nullable',
                'integer',
                Rule::exists('counselor_referrers', 'id')->whereNotNull('id')
            ],
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'targets' => 'required|array|min:1',
            'targets.*.min_target' => 'required|integer|min:1',
            'targets.*.max_target' => 'required|integer|gt:targets.*.min_target',
            'targets.*.amount_percentage' => 'nullable|string',
            'targets.*.type' => 'nullable|string',
        ]);

        try {
            $targets = [];

            foreach ($validatedData['targets'] as $targetData) {
                $target = Target::create([
                    'counselor_referrer_id' => $request->counselor_referrer_id,
                    'academic_year_id' => $request->academic_year_id,
                    'min_target' => $targetData['min_target'],
                    'max_target' => $targetData['max_target'],
                    'amount_percentage' => $targetData['amount_percentage'] ?? null,
                    'type' => $targetData['type'] ?? null,
                ]);

                $targets[] = $target;
            }

            return response()->json([
                'message' => 'Targets added successfully',
                'data' => $targets,
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
    }
    /**
     * Update Target
     * @OA\Put (
     *     path="/api/v1/target/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Target"},
     *     summary="Update an existing Target",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="counselor_referrer_id", type="integer"),
     *                 @OA\Property(property="academic_year_id", type="integer"),
     *                 @OA\Property(property="min_target", type="integer"),
     *                 @OA\Property(property="max_target", type="integer"),
     *                 @OA\Property(property="amount_percentage", type="string"),
     *                 @OA\Property(property="type", type="string")
     *             ),
     *             example={
     *                 "counselor_referrer_id": 1,
     *                 "academic_year_id": 2,
     *                 "min_target": 10,
     *                 "max_target": 20,
     *                 "amount_percentage": "10%",
     *                 "type": "percentage"
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Target updated successfully"),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-10T10:00:00.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-10T10:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Target not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Target not found"),
     *             @OA\Property(property="status", type="integer", example=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *             @OA\Property(property="error", type="string", example="Unexpected error occurred")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $data = Target::find($id);
        if (!$data) {
            return response()->json([
                'message' => 'Target not found',
                'status' => 0
            ], 404);
        }
        $validatedData = $request->validate([
            'counselor_referrer_id' => 'nullable|integer',
            'academic_year_id' => 'required|integer',
            'min_target' => 'required|integer|min:1',
            'max_target' => 'required|integer|gt:min_target',
            'amount_percentage' => 'nullable|string',
            'type' => 'nullable|string',
        ]);
        DB::beginTransaction();
        try {
            $data->update($validatedData);
            DB::commit();
            return response()->json([
                'message' => 'Target updated successfully',
                'status' => 1,
                'data' => $data
            ], 200);
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Internal Server Error: ' . $err->getMessage(),
                'status' => 0
            ], 500);
        }
    }
    /**
     * Delete Target
     * @OA\Delete (
     *     path="/api/v1/target/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Target"},
     *     summary="Delete a Target",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Target  deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="delete todo success")
     *         )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Target not found"
     *      )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $data = Target::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message' => 'Target does not exits',
                'status' => 0,
            ];
            $respCode = 404;
        } else {
            DB::beginTransaction();
            try {
                $data->delete();
                DB::commit();
                $response = [
                    'message' => 'Target deleted successfully',
                    'status' => 1,
                ];
                $respCode = 200;
            } catch (\Exception $err) {
                DB::rollBack();
                $response = [
                    'message' => 'Internal Server error',
                    'status' => 0,
                ];
                $respCode = 500;
            }
        }
        return response()->json($response, $respCode);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/target/{id}",
     *      security={{"Bearer": {}}},
     *      tags={"Target"},
     *      summary="Get single Target",
     *      description="Returns list of Target",
     *          @OA\Parameter(
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *       @OA\Response(response=400, description="Bad request"),
     *     )
     */
    public function show($id)
    {
        $data = Target::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message' => 'Target not found',
                'status' => 0,
            ];
        } else {
            $response = [
                'message' => 'Target found',
                'status' => 1,
                'data' => $data
            ];
        }
        return response()->json($response, 200);
    }
}
