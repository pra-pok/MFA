<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\CounselorReferrer;
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

class AcademicYearRestApiController extends Controller
{
    /**
     * Get List of Academic Years with Pagination & Limit
     * @OA\Get (
     *     path="/api/v1/academic/year",
     *     security={{"Bearer": {}}},
     *     tags={"Academic Year"},
     *     summary="Retrieve a list of Academic Years with pagination",
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
     *                     @OA\Property(property="name", type="string", example="2024-2025"),
     *                     @OA\Property(property="effective_from", type="string", example="2024-01-01"),
     *                     @OA\Property(property="valid_till", type="string", example="2024-12-31"),
     *                     @OA\Property(property="created_by", type="integer", example=1),
     *                     @OA\Property(property="updated_by", type="integer", example=1),
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
     *             @OA\Property(property="message", type="string", example="Academic Year retrieved successfully"),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-03-10T12:00:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No Academic Year found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No Academic Year found"),
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
            $query = AcademicYear::query()->orderBy('id', 'desc');
            if ($limit) {
                $total = $query->count();
                $academicYears = $query->limit($limit)->offset($offset)->get();
                $meta = [
                    'total' => $total,
                    'per_page' => (int) $limit,
                    'current_page' => (int) ceil(($offset + 1) / $limit),
                    'last_page' => (int) ceil($total / $limit),
                    'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                    'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
                ];
            } else {
                $paginatedAcademicYears = $query->paginate($perPage);
                $meta = [
                    'total' => $paginatedAcademicYears->total(),
                    'per_page' => $paginatedAcademicYears->perPage(),
                    'current_page' => $paginatedAcademicYears->currentPage(),
                    'last_page' => $paginatedAcademicYears->lastPage(),
                    'next_page_url' => $paginatedAcademicYears->nextPageUrl(),
                    'prev_page_url' => $paginatedAcademicYears->previousPageUrl(),
                ];
                $academicYears = collect($paginatedAcademicYears->items());
            }
            return response()->json([
                'data' => $academicYears,
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
     * Store a new Academic Year
     * @OA\Post (
     *     path="/api/v1/academic/year",
     *     security={{"Bearer": {}}},
     *     tags={"Academic Year"},
     *     summary="Create a new Academic Year",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", example="2024-2025"),
     *                 @OA\Property(property="effective_from", type="string", format="date", example="2024-01-01"),
     *                 @OA\Property(property="valid_till", type="string", format="date", example="2024-12-31"),
     *                 @OA\Property(property="is_current", type="boolean", example=1),
     *                 @OA\Property(property="created_by", type="integer", example=1),
     *                 @OA\Property(property="updated_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Academic Year added successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="2024-2025"),
     *                 @OA\Property(property="effective_from", type="string", format="date", example="2024-01-01"),
     *                 @OA\Property(property="valid_till", type="string", format="date", example="2024-12-31"),
     *                 @OA\Property(property="is_current", type="boolean", example=1),
     *                 @OA\Property(property="created_by", type="integer", example=1),
     *                 @OA\Property(property="updated_by", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-10T10:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-10T10:00:00.000000Z")
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
        $organization = Auth::user();
        $request->request->add(['created_by' =>$organization->id]);
        $request->request->add(['updated_by' =>$organization->id]);
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'effective_from' => 'nullable|date',
                'valid_till' => 'nullable|date',
                'is_current' => 'nullable|boolean',
                'created_by' => 'nullable',
                'updated_by' => 'nullable',
            ]);
            $data = AcademicYear::create($validatedData);
            return response()->json([
                'message' => 'Academic Year added successfully',
                'data' => $data,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
    }

    /**
     * Update Academic Year
     * @OA\Put (
     *     path="/api/v1/academic/year/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Academic Year"},
     *     summary="Update an existing Academic Year",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                 ),
     *                 example={
     *                     "name":"example name",
     *                     "effective_from":"2025-01-01",
     *                     "valid_till":"2025-12-31",
     *                     "is_current":1
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="name"),
     *              @OA\Property(property="effective_from", type="string", example="2025-01-01"),
     *              @OA\Property(property="valid_till", type="string", example="2025-12-31"),
     *              @OA\Property(property="is_current", type="boolean", example=1),
     *              @OA\Property(property="created_by", type="number", example="created_by"),
     *              @OA\Property(property="updated_by", type="number", example="updated_by"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $organization = Auth::user();
        $request->request->add(['created_by' =>$organization->id]);
        $request->request->add(['updated_by' =>$organization->id]);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'effective_from' => 'nullable|date',
            'valid_till' => 'nullable|date',
            'is_current' => 'nullable|boolean',
            'created_by' => 'nullable',
            'updated_by' => 'nullable',
        ]);
        $data = AcademicYear::find($id);
        if (!$data) {
            return response()->json([
                'message' => 'Academic Year not found',
                'status' => 0
            ], 404);
        }
        DB::beginTransaction();
        try {
            $data->update($validatedData);
            DB::commit();
            return response()->json([
                'message' => 'Academic Year updated successfully',
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
     * Delete Academic Year
     * @OA\Delete (
     *     path="/api/v1/academic/year/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Academic Year"},
     *     summary="Delete a Academic Year",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Academic Year  deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="delete todo success")
     *         )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Academic Year not found"
     *      )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $data = AcademicYear::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message' => 'Academic Year does not exits',
                'status' => 0,
            ];
            $respCode = 404;
        } else {
            DB::beginTransaction();
            try {
                $data->delete();
                DB::commit();
                $response = [
                    'message' => 'Academic Year deleted successfully',
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
     *     path="/api/v1/academic/year/{id}",
     *      security={{"Bearer": {}}},
     *      tags={"Academic Year"},
     *      summary="Get single  Academic Year",
     *      description="Returns list of Academic Year",
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
        $data = AcademicYear::with('createds:id,username', 'updatedBy:id,username')
            ->findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message' => 'Academic Year not found',
                'status' => 0,
            ];
        } else {
            $response = [
                'message' => 'Academic Year found',
                'status' => 1,
                'data' => $data
            ];
        }
        return response()->json($response, 200);
    }
}
