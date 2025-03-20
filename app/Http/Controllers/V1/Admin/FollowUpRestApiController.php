<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CounselorReferrer;
use App\Models\FollowUp;
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

class FollowUpRestApiController extends Controller
{
    /**
     * Get List of Follow Up
     * @OA\Get (
     *     path="/api/v1/followup",
     *     security={{"Bearer": {}}},
     *     tags={"Follow Up"},
     *     summary="Retrieve a list of follow-ups",
     *           @OA\Parameter(
     *            name="per_page",
     *            in="query",
     *            description="Number of items per page (for pagination)",
     *            required=false,
     *            @OA\Schema(type="integer", default=10, example=10)
     *        ),
     *        @OA\Parameter(
     *            name="limit",
     *            in="query",
     *            description="Number of items to retrieve",
     *            required=false,
     *            @OA\Schema(type="integer", example=5)
     *        ),
     *        @OA\Parameter(
     *            name="offset",
     *            in="query",
     *            description="Number of items to skip (used with limit)",
     *            required=false,
     *            @OA\Schema(type="integer", example=0)
     *        ),
     *       @OA\Parameter(
     *             name="keyword",
     *             in="query",
     *             description="Search by date",
     *             required=false,
     *             @OA\Schema(type="string", example="2025")
     *         ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="data",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="_id", type="integer", example=1),
     *                     @OA\Property(property="date", type="string", format="date-time", example="2024-03-10T14:30:00Z"),
     *                     @OA\Property(property="via", type="string", example="Email"),
     *                     @OA\Property(property="note", type="string", example="Follow-up call scheduled"),
     *                     @OA\Property(property="next_date_time", type="string", format="date-time", example="2024-03-15T10:00:00Z"),
     *                     @OA\Property(property="is_current_status", type="boolean", example=true),
     *                     @OA\Property(property="student_id", type="integer", example=5),
     *                     @OA\Property(property="status_id", type="integer", example=2),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-10T14:30:00Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-10T14:30:00Z")
     *                 )
     *             )
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
            $keyword = $request->input('keyword');

            $query = FollowUp::with('student:id,name,email', 'status:id,title,color,note')->orderBy('id', 'desc');

            if (!empty($keyword)) {
                $query->where('date', 'LIKE', "%{$keyword}%")
                    ->orWhere('note', 'LIKE', "%{$keyword}")
                ; // Adjust the column name for date as needed
            }
            if ($limit) {
                $total = $query->count();
                $followUps = $query->limit($limit)->offset($offset)->get();
                $meta = [
                    'total' => $total,
                    'per_page' => (int) $limit,
                    'current_page' => (int) ceil(($offset + 1) / $limit),
                    'last_page' => (int) ceil($total / $limit),
                    'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                    'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
                ];
            } else {
                $paginatedFollowUps = $query->paginate($perPage);
                $meta = [
                    'total' => $paginatedFollowUps->total(),
                    'per_page' => $paginatedFollowUps->perPage(),
                    'current_page' => $paginatedFollowUps->currentPage(),
                    'last_page' => $paginatedFollowUps->lastPage(),
                    'next_page_url' => $paginatedFollowUps->nextPageUrl(),
                    'prev_page_url' => $paginatedFollowUps->previousPageUrl(),
                ];
                $followUps = collect($paginatedFollowUps->items());
            }

            return response()->json([
                'data' => $followUps,
                'meta' => $meta,
                'message' => 'Follow Up retrieved successfully',
                'status' => 1,
                'timestamp' => now()->toISOString(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Store a new Follow-Up
     * @OA\Post (
     *     path="/api/v1/followup",
     *     security={{"Bearer": {}}},
     *     tags={"Follow Up"},
     *     summary="Create a new Follow Up",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"student_id", "date", "via"},
     *                 @OA\Property(property="student_id", type="integer", example=1),
     *                 @OA\Property(property="date", type="string", format="date-time", example="2025-03-09 11:14:43"),
     *                 @OA\Property(property="via", type="string", example="Email"),
     *                 @OA\Property(property="note", type="string", nullable=true, example="Follow-up call scheduled"),
     *                 @OA\Property(property="status_id", type="integer", nullable=true, example=2),
     *                 @OA\Property(property="is_current_status", type="boolean", nullable=true, example=true),
     *                 @OA\Property(property="next_date_time", type="string", format="date-time", nullable=true, example="2025-03-09 11:14:43"),
     *                 example={
     *                     "student_id": 1,
     *                     "date": "2025-03-09 11:14:43",
     *                     "via": "Email",
     *                     "note": "Follow-up call scheduled",
     *                     "status_id": 2,
     *                     "is_current_status": true,
     *                     "next_date_time": "2025-03-09 11:14:43",
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="student_id", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date-time", example="2025-03-09 11:14:43"),
     *             @OA\Property(property="via", type="string", example="Email"),
     *             @OA\Property(property="note", type="string", example="Follow-up call scheduled"),
     *             @OA\Property(property="status_id", type="integer", example=2),
     *             @OA\Property(property="is_current_status", type="boolean", example=true),
     *             @OA\Property(property="next_date_time", type="string", format="date-time", example="2025-03-09 11:14:43"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-09 11:14:43"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-09 11:14:43")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'student_id' => 'required|integer',
                'date' => 'required|date',
                'via' => 'required|string',
                'note' => 'nullable|string',
                'status_id' => 'nullable|integer',
                'is_current_status' => 'nullable|boolean',
                'next_date_time' => 'nullable|date',
            ]);
            $data = FollowUp::create($validatedData);
            return response()->json([
                'message' => 'Follow Up added successfully',
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
     * Update Follow Up
     * @OA\Put (
     *     path="/api/v1/followup/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Follow Up"},
     *     summary="Update an existing Follow Up",
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
     *                 required={"student_id", "date", "via"},
     *                 @OA\Property(property="student_id", type="integer", example=1),
     *                 @OA\Property(property="date", type="string", format="date-time", example="2024-03-10T14:30:00Z"),
     *                 @OA\Property(property="via", type="string", example="Email"),
     *                 @OA\Property(property="note", type="string", nullable=true, example="Follow-up call scheduled"),
     *                 @OA\Property(property="status_id", type="integer", nullable=true, example=2),
     *                 @OA\Property(property="is_current_status", type="boolean", nullable=true, example=true),
     *                 @OA\Property(property="next_date_time", type="string", format="date-time", nullable=true, example="2024-03-15T10:00:00Z"),
     *                 example={
     *                     "student_id": 1,
     *                     "date": "2024-03-10T14:30:00Z",
     *                     "via": "Email",
     *                     "note": "Follow-up call scheduled",
     *                     "status_id": 2,
     *                     "is_current_status": true,
     *                     "next_date_time": "2024-03-15T10:00:00Z"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="student_id", type="integer", example=1),
     *             @OA\Property(property="date", type="string", format="date-time", example="2024-03-10T14:30:00Z"),
     *             @OA\Property(property="via", type="string", example="Email"),
     *             @OA\Property(property="note", type="string", example="Follow-up call scheduled"),
     *             @OA\Property(property="status_id", type="integer", example=2),
     *             @OA\Property(property="is_current_status", type="boolean", example=true),
     *             @OA\Property(property="next_date_time", type="string", format="date-time", example="2024-03-15T10:00:00Z"),
     *             @OA\Property(property="updated_by", type="integer", example=1),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-10T14:30:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Follow Up not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Follow Up not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'student_id' => 'required|integer',
            'date' => 'required|date',
            'via' => 'required|string',
            'note' => 'nullable|string',
            'status_id' => 'nullable|integer',
            'is_current_status' => 'nullable|boolean',
            'next_date_time' => 'nullable|date',
        ]);
        $data = FollowUp::find($id);
        if (!$data) {
            return response()->json([
                'message' => 'Follow Up not found',
                'status' => 0
            ], 404);
        }
        DB::beginTransaction();
        try {
            $data->update($validatedData);
            DB::commit();
            return response()->json([
                'message' => 'Follow Up updated successfully',
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
     * Delete Follow Up
     * @OA\Delete (
     *     path="/api/v1/followup/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Follow Up"},
     *     summary="Delete a Follow Up",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Follow Up  deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="delete todo success")
     *         )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Follow Up not found"
     *      )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $data = FollowUp::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message' => 'Follow Up does not exits',
                'status' => 0,
            ];
            $respCode = 404;
        } else {
            DB::beginTransaction();
            try {
                $data->delete();
                DB::commit();
                $response = [
                    'message' => 'Follow Up deleted successfully',
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
     *     path="/api/v1/followup/{id}",
     *      security={{"Bearer": {}}},
     *      tags={"Follow Up"},
     *      summary="Get single Follow Up",
     *      description="Returns list of Follow Up",
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
        $data = FollowUp::with('student:id,name,email' ,'status:id,title,color,note')->findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message' => 'Follow Up not found',
                'status' => 0,
            ];
        } else {
            $response = [
                'message' => 'Follow Up found',
                'status' => 1,
                'data' => $data
            ];
        }
        return response()->json($response, 200);
    }
}
