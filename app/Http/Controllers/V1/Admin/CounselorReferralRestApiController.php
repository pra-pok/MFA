<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
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

class CounselorReferralRestApiController extends Controller
{

    /**
     * Get List Counselor Referral
     * @OA\Get (
     *     path="/api/v1/counselor/referral/",
     *     security={{"Bearer": {}}},
     *     tags={"Counselor Referral"},
     *     summary="Retrieve a list of counselor referrals",
     *    @OA\Parameter(
     *           name="per_page",
     *           in="query",
     *           description="Number of items per page (for pagination)",
     *           required=false,
     *           @OA\Schema(type="integer", default=10, example=10)
     *       ),
     *       @OA\Parameter(
     *           name="limit",
     *           in="query",
     *           description="Number of items to retrieve",
     *           required=false,
     *           @OA\Schema(type="integer", example=5)
     *       ),
     *       @OA\Parameter(
     *           name="offset",
     *           in="query",
     *           description="Number of items to skip (used with limit)",
     *           required=false,
     *           @OA\Schema(type="integer", example=0)
     *       ),
     *      @OA\Parameter(
     *            name="keyword",
     *            in="query",
     *            description="Search by name",
     *            required=false,
     *            @OA\Schema(type="string", example="shivam")
     *        ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="_id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="example name"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="example email"
     *                     ),
     *                      @OA\Property(
     *                         property="phone",
     *                         type="string",
     *                         example="example phone"
     *                     ),
     *                 @OA\Property(
     *                    property="address",
     *                    type="string",
     *                    example="example address"
     *                  ),
     *                  @OA\Property(
     *                   property="role",
     *                   type="string",
     *                   example="example role"
     *                 ),
     *                 @OA\Property(
     *                property="created_by",
     *                type="string",
     *                example="example created_by"
     *                 ),
     *              @OA\Property(
     *              property="updated_by",
     *              type="string",
     *              example="example updated_by"
     *             ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2021-12-11T09:25:53.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2021-12-11T09:25:53.000000Z"
     *                     )
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
            $query = CounselorReferrer::query()->orderBy('id', 'desc');
            // Filter by keyword if present
            if (!empty($keyword)) {
                $query->where('name', 'LIKE', "%{$keyword}%");
            }
            // Fetch data based on limit or per_page
            if ($limit) {
                $total = $query->count();
                $counselors = $query->limit($limit)->offset($offset)->get();
                $meta = [
                    'total' => $total,
                    'per_page' => (int) $limit,
                    'current_page' => (int) ceil(($offset + 1) / $limit),
                    'last_page' => (int) ceil($total / $limit),
                    'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                    'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
                ];
            } else {
                $paginatedCounselors = $query->paginate($perPage);
                $meta = [
                    'total' => $paginatedCounselors->total(),
                    'per_page' => $paginatedCounselors->perPage(),
                    'current_page' => $paginatedCounselors->currentPage(),
                    'last_page' => $paginatedCounselors->lastPage(),
                    'next_page_url' => $paginatedCounselors->nextPageUrl(),
                    'prev_page_url' => $paginatedCounselors->previousPageUrl(),
                ];
                $counselors = collect($paginatedCounselors->items());
            }
            // Format the data
            $formattedCounselors = $counselors->map(function ($item) {
                return [
                    '_id' => $item->id,
                    'name' => $item->name,
                    'email' => $item->email,
                    'phone' => $item->phone,
                    'address' => $item->address,
                    'role' => $item->role,
                    'created_by' => $item->createds ? [
                        'id' => $item->createds->id,
                        'username' => $item->createds->username,
                    ] : null,
                    'updated_by' => $item->updatedBy ? [
                        'id' => $item->updatedBy->id,
                        'username' => $item->updatedBy->username,
                    ] : null,
                    'created_at' => $item->created_at->toDateTimeString(),
                    'updated_at' => $item->updated_at->toDateTimeString(),
                ];
            });

            return response()->json([
                'message' => 'Counselor Referral retrieved successfully',
                'status' => 1,
                'data' => $formattedCounselors,
                'meta' => $meta
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
     * Store a new Counselor Referral
     * @OA\Post (
     *     path="/api/v1/counselor/referral",
     *     security={{"Bearer": {}}},
     *     tags={"Counselor Referral"},
     *     summary="Create a new counselor referral",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="role", type="string", enum={"Counselor", "Agent", "Referrer"}),
     *                 @OA\Property(property="created_by", type="integer"),
     *                 example={
     *                     "name": "example name",
     *                     "email": "example email",
     *                     "phone": "example phone",
     *                     "address": "example address",
     *                     "role": "Counselor",
     *                     "created_by": 1
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="number", example=1),
     *             @OA\Property(property="name", type="string", example="name"),
     *             @OA\Property(property="email", type="string", example="email"),
     *             @OA\Property(property="phone", type="string", example="phone"),
     *             @OA\Property(property="address", type="string", example="address"),
     *             @OA\Property(property="role", type="string", example="Counselor"),
     *             @OA\Property(property="created_by", type="number", example=1),
     *             @OA\Property(property="updated_by", type="number", example=1),
     *             @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *             @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
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
        $organization = Auth::user();
        $request->request->add(['created_by' =>$organization->id]);
        $request->request->add(['updated_by' =>$organization->id]);
        //$data['role'] = ['Counselor' => 'Counselor', 'Agent' => 'Agent', 'Referrer' => 'Referrer'];
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'required|string|max:255',
                'address' => 'nullable|string',
                'role' => 'nullable|string|in:Counselor,Agent,Referrer',
                'created_by' => 'nullable',
            ]);
            $data = CounselorReferrer::create($validatedData);
            return response()->json([
                'message' => 'Counselor Referral added successfully',
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
     * Update Counselor Referral
     * @OA\Put (
     *     path="/api/v1/counselor/referral/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Counselor Referral"},
     *     summary="Update an existing Counselor Referral",
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
     *                      @OA\Property(
     *                          property="content",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"example name",
     *                     "email":"example email",
     *                     "phone":"example phone",
     *                     "address":"example address",
     *                     "role":"example role",
     *                     "created_by":"example created_by",
     *                     "updated_by":"example updated_by"
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
     *              @OA\Property(property="email", type="string", example="email"),
     *              @OA\Property(property="phone", type="string", example="phone"),
     *              @OA\Property(property="address", type="string", example="address"),
     *              @OA\Property(property="role", type="string", example="role"),
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
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string',
            'role' => 'nullable|string',
            'created_by' => 'nullable',
            'updated_by' => 'nullable',
        ]);
        $data = CounselorReferrer::find($id);
        if (!$data) {
            return response()->json([
                'message' => 'Counselor Referral not found',
                'status' => 0
            ], 404);
        }
        DB::beginTransaction();
        try {
            $data->update($validatedData);
            DB::commit();
            return response()->json([
                'message' => 'Counselor Referral updated successfully',
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
     * Delete Counselor Referral
     * @OA\Delete (
     *     path="/api/v1/counselor/referral/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Counselor Referral"},
     *     summary="Delete a Counselor Referral",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Counselor Referral  deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="delete todo success")
     *         )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Counselor Referral not found"
     *      )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $data = CounselorReferrer::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message' => 'Counselor Referral does not exits',
                'status' => 0,
            ];
            $respCode = 404;
        } else {
            DB::beginTransaction();
            try {
                $data->delete();
                DB::commit();
                $response = [
                    'message' => 'Counselor Referral deleted successfully',
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
     *     path="/api/v1/counselor/referral/{id}",
     *      security={{"Bearer": {}}},
     *      tags={"Counselor Referral"},
     *      summary="Get single  Counselor Referral",
     *      description="Returns list of Counselor Referral",
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
        $data = CounselorReferrer::with(['createds', 'updatedBy'])->findOrFail($id);

        // Format the data properly
        $formattedCounselor = [
            'id' => $data->id,
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'address' => $data->address,
            'role' => $data->role,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
            'created_by' => $data->createds ? [
                'id' => $data->createds->id,
                'username' => $data->createds->username,
            ] : null,
            'updated_by' => $data->updatedBy ? [
                'id' => $data->updatedBy->id,
                'username' => $data->updatedBy->username,
            ] : null,
        ];

        return response()->json([
            'message' => 'Counselor Referral found',
            'status' => 1,
            'data' => $formattedCounselor
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/counselor/",
     *     summary="list of counselor referrals role",
     *     tags={"Config Search"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function counselor()
    {
        $data['role'] = ['Counselor' => 'Counselor', 'Agent' => 'Agent', 'Referrer' => 'Referrer'];
        return response()->json([
            'message' => '',
            'status' => 1,
            'data' => $data
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/config/counselor/referral",
     *     summary="Get active Counselor and Referrer",
     *     tags={"Config Search"},
     *     security={{"Bearer": {}}},
     *     description="Fetches a list of active Counselors and Referrers ordered by latest entries.",
     *     @OA\Parameter(
     *     name="keyword",
     *     in="query",
     *     description="Search keyword to filter counselors and referrers by name or email",
     *     required=false,
     *      @OA\Schema(type="string")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Counselor Referral retrieved successfully"),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-03-03T12:00:00Z"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="counselor",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="referrer",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="name", type="string", example="Jane Smith")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No Counselor or Referrer found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No Counselor or Referrer available"),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-03-03T12:00:00Z"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="counselor", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="referrer", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function configCounselorReferral(Request $request)
    {
        $keyword = $request->query('keyword'); // Get the keyword from the request

        // Query for Counselors
        $counselorQuery = CounselorReferrer::where('role', 'Counselor');
        if ($keyword) {
            $counselorQuery->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%")
                    ->orWhere('phone', 'LIKE', "%$keyword%");
            });
        } else {
            $counselorQuery->limit(3); // Show only 3 by default
        }
        $counselor = $counselorQuery->orderBy('id', 'desc')
            ->select('id', 'name', 'role', 'email', 'phone')
            ->get();

        // Query for Referrers
        $referrerQuery = CounselorReferrer::where('role', 'Referrer');
        if ($keyword) {
            $referrerQuery->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', "%$keyword%")
                    ->orWhere('email', 'LIKE', "%$keyword%")
                    ->orWhere('phone', 'LIKE', "%$keyword%");
            });
        } else {
            $referrerQuery->limit(3); // Show only 3 by default
        }
        $referrer = $referrerQuery->orderBy('id', 'desc')
            ->select('id', 'name', 'role', 'email', 'phone')
            ->get();

        // If both are empty, return a 404 response
        if ($counselor->isEmpty() && $referrer->isEmpty()) {
            return response()->json([
                'message' => 'No Counselor or Referrer found',
                'status' => 0,
                'data' => [
                    'counselor' => [],
                    'referrer' => []
                ]
            ], 404);
        }

        // Return the result
        return response()->json([
            'message' => 'Counselor Referral retrieved successfully',
            'status' => 1,
            'data' => [
                'counselor' => $counselor,
                'referrer' => $referrer
            ]
        ], 200);
    }
}
