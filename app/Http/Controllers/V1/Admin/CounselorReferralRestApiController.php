<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CounselorReferrer;
use App\Models\Target;
use App\Models\TargetGroup;
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
     *         description="Successful operation",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Counselor Referral found"),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+1234567890"),
     *                 @OA\Property(property="address", type="string", example="123 Street Name"),
     *                 @OA\Property(property="role", type="string", example="Counselor"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2022-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2022-01-02T00:00:00Z"),
     *                 @OA\Property(
     *                     property="created_by",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="username", type="string", example="admin")
     *                 ),
     *                 @OA\Property(
     *                     property="updated_by",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="username", type="string", example="manager")
     *                 ),
     *                 @OA\Property(
     *                     property="target_groups",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=4),
     *                         @OA\Property(property="academic_year_id", type="integer", example=1),
     *                         @OA\Property(property="academic_year_name", type="string", example="2021-2022"),
     *                         @OA\Property(
     *                             property="targets",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=7),
     *                                 @OA\Property(property="min_target", type="integer", example=20),
     *                                 @OA\Property(property="max_target", type="integer", example=30),
     *                                 @OA\Property(property="amount_percentage", type="string", example="40%"),
     *                                 @OA\Property(property="type", type="string", example="Percentage"),
     *                                 @OA\Property(property="per_student", type="integer", example=1)
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $limit = $request->input('limit');
            $offset = $request->input('offset', 0);
            $keyword = $request->input('keyword');

            $query = CounselorReferrer::with(['targetGroups.academicYear', 'targetGroups.targets', 'createds', 'updatedBy'])
                ->orderBy('id', 'desc');

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
                    'target_groups' => $item->targetGroups->map(function ($group) {
                        return [
                            'id' => $group->id,
                            'academic_year_id' => $group->academic_year_id,
                            'academic_year_name' => $group->academicYear ? $group->academicYear->name : null,
                            'targets' => $group->targets->map(function ($target) {
                                return [
                                    'id' => $target->id,
                                    'min_target' => $target->min_target,
                                    'max_target' => $target->max_target,
                                    'amount_percentage' => $target->amount_percentage,
                                    'type' => $target->type,
                                    'per_student' => $target->per_student,
                                ];
                            }),
                        ];
                    }),
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
     * Store a new Counselor Referral with Targets
     *
     * @OA\Post(
     *     path="/api/v1/counselor/referral",
     *     security={{"Bearer": {}}},
     *     tags={"Counselor Referral"},
     *     summary="Create a new counselor referral along with multiple targets",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="role", type="string", enum={"Counselor", "Agent", "Referrer"}),
     *                 @OA\Property(property="academic_year_id", type="integer"),
     *                 @OA\Property(property="targets", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="min_target", type="integer"),
     *                         @OA\Property(property="max_target", type="integer"),
     *                         @OA\Property(property="amount_percentage", type="string"),
     *                         @OA\Property(property="type", type="string"),
     *                         @OA\Property(property="per_student", type="boolean")
     *                     )
     *                 ),
     *                 example={
     *                     "name": "John Doe",
     *                     "email": "johndoe@example.com",
     *                     "phone": "+1234567890",
     *                     "address": "123 Street, City",
     *                     "role": "Counselor",
     *                     "academic_year_id": 1,
     *                     "targets": {
     *                         {
     *                             "min_target": 10,
     *                             "max_target": 20,
     *                             "amount_percentage": "10%",
     *                             "type": "Percentage",
     *                             "per_student": 1
     *                         },
     *                         {
     *                             "min_target": 21,
     *                             "max_target": 30,
     *                             "amount_percentage": "15000",
     *                             "type": "Amount",
     *                             "per_student": 0
     *                         }
     *                     }
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Counselor Referral and Targets created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Counselor Referral added successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+1234567890"),
     *                 @OA\Property(property="role", type="string", example="Counselor"),
     *                 @OA\Property(property="created_by", type="integer", example=1),
     *                 @OA\Property(property="updated_by", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", example="2025-03-21T09:25:53.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-03-21T09:25:53.000000Z"),
     *                 @OA\Property(property="targets", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="min_target", type="integer", example=10),
     *                         @OA\Property(property="max_target", type="integer", example=20),
     *                         @OA\Property(property="amount_percentage", type="string", example="10%"),
     *                         @OA\Property(property="type", type="string", example="Percentage"),
     *                         @OA\Property(property="per_student", type="boolean", example=1)
     *                     )
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
     *             @OA\Property(property="error", type="string", example="Exception message here")
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
            // Validate CounselorReferrer Data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'required|string|max:255',
                'address' => 'nullable|string',
                'role' => 'nullable|string|in:Counselor,Agent,Referrer',
                'created_by' => 'nullable',
                'academic_year_id' => 'required|integer|exists:academic_years,id',
                'targets' => 'required|array|min:1',
                'targets.*.min_target' => 'required|integer|min:1',
                'targets.*.max_target' => 'required|integer|gt:targets.*.min_target',
                'targets.*.amount_percentage' => 'nullable|string',
                'targets.*.type' => 'nullable|string',
                'targets.*.per_student' => 'nullable|boolean',
            ]);

            DB::beginTransaction();

            // Create CounselorReferrer
            $counselorReferrer = CounselorReferrer::create($validatedData);

            // Create TargetGroup
            $targetGroup = TargetGroup::create([
                'counselor_referrer_id' => $counselorReferrer->id,
                'academic_year_id' => $validatedData['academic_year_id'],
            ]);

            // Add Targets
            foreach ($validatedData['targets'] as $targetData) {
                $targetGroup->targets()->create([
                    'min_target' => $targetData['min_target'],
                    'max_target' => $targetData['max_target'],
                    'amount_percentage' => $targetData['amount_percentage'] ?? null,
                    'type' => $targetData['type'] ?? null,
                    'per_student' => $targetData['per_student'] ?? null,
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => 'Counselor Referral and Targets added successfully',
                'data' => $counselorReferrer->load('targetGroups.targets'),
            ], 201);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
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
     * @OA\Put(
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
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="role",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="target_groups",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(
     *                             property="academic_year_id",
     *                             type="integer"
     *                         ),
     *                         @OA\Property(
     *                             property="targets",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(
     *                                     property="min_target",
     *                                     type="integer"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="max_target",
     *                                     type="integer"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="amount_percentage",
     *                                     type="integer"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="type",
     *                                     type="string"
     *                                 ),
     *                                 @OA\Property(
     *                                     property="per_student",
     *                                     type="integer"
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 example={
     *                     "name": "example name",
     *                     "email": "example email",
     *                     "phone": "example phone",
     *                     "address": "example address",
     *                     "role": "example role",
     *                     "target_groups":
     *                         {
     *                             "academic_year_id": 1,
     *                             "targets":
     *                                 {
     *                                     "min_target": 50,
     *                                     "max_target": 100,
     *                                     "amount_percentage": 10,
     *                                     "type": "percentage",
     *                                     "per_student": 5
     *                                 }
     *
     *                         }
     *
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="phone", type="string", example="+1234567890"),
     *             @OA\Property(property="role", type="string", example="Counselor"),
     *             @OA\Property(property="created_by", type="integer", example=1),
     *             @OA\Property(property="updated_by", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", example="2025-03-21T09:25:53.000000Z"),
     *             @OA\Property(property="updated_at", type="string", example="2025-03-21T09:25:53.000000Z"),
     *             @OA\Property(
     *                 property="targets",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="min_target", type="integer", example=10),
     *                     @OA\Property(property="max_target", type="integer", example=20),
     *                     @OA\Property(property="amount_percentage", type="string", example="10%"),
     *                     @OA\Property(property="type", type="string", example="Percentage"),
     *                     @OA\Property(property="per_student", type="boolean", example=1)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        // Authentication and adding user IDs for the operation
        $organization = Auth::user();
        $request->request->add(['created_by' => $organization->id]);
        $request->request->add(['updated_by' => $organization->id]);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string',
            'role' => 'nullable|string',
            'target_groups' => 'required|array',  // Ensure target_groups is an array
            'target_groups.*.academic_year_id' => 'required|integer|exists:academic_years,id',
            'target_groups.*.targets' => 'required|array',
            'target_groups.*.targets.*.min_target' => 'required|integer',
            'target_groups.*.targets.*.max_target' => 'required|integer',
            'target_groups.*.targets.*.amount_percentage' => 'required|integer',
            'target_groups.*.targets.*.type' => 'required|string',
            'target_groups.*.targets.*.per_student' => 'required|integer',
        ]);

        // Find the counselor referral record
        $data = CounselorReferrer::find($id);
        if (!$data) {
            return response()->json([
                'message' => 'Counselor Referral not found',
                'status' => 0
            ], 404);
        }

        // Begin database transaction
        DB::beginTransaction();
        try {
            // Update the counselor referral data
            $data->update($validatedData);

            // Handle target group and target updates or additions
            foreach ($validatedData['target_groups'] as $groupData) {
                // Update or create target group
                $group = $data->targetGroups()->updateOrCreate(
                    ['academic_year_id' => $groupData['academic_year_id']],
                    ['academic_year_id' => $groupData['academic_year_id']]
                );

                // Handle targets for the group
                foreach ($groupData['targets'] as $targetData) {
                    $group->targets()->updateOrCreate(
                        ['id' => $targetData['id'] ?? null], // Use the target ID if exists
                        [
                            'min_target' => $targetData['min_target'],
                            'max_target' => $targetData['max_target'],
                            'amount_percentage' => $targetData['amount_percentage'],
                            'type' => $targetData['type'],
                            'per_student' => $targetData['per_student'],
                        ]
                    );
                }
            }

            // Commit the transaction
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
     *     security={{"Bearer": {}}},
     *     tags={"Counselor Referral"},
     *     summary="Get single Counselor Referral",
     *     description="Returns a Counselor Referral with target groups, academic year, and targets",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Counselor Referral found"),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="+1234567890"),
     *                 @OA\Property(property="address", type="string", example="123 Street Name"),
     *                 @OA\Property(property="role", type="string", example="Counselor"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2022-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2022-01-02T00:00:00Z"),
     *                 @OA\Property(
     *                     property="created_by",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="username", type="string", example="admin")
     *                 ),
     *                 @OA\Property(
     *                     property="updated_by",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="username", type="string", example="manager")
     *                 ),
     *                 @OA\Property(
     *                     property="target_groups",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=4),
     *                         @OA\Property(property="academic_year_id", type="integer", example=1),
     *                         @OA\Property(property="academic_year_name", type="string", example="2021-2022"),
     *                         @OA\Property(
     *                             property="targets",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=7),
     *                                 @OA\Property(property="min_target", type="integer", example=20),
     *                                 @OA\Property(property="max_target", type="integer", example=30),
     *                                 @OA\Property(property="amount_percentage", type="string", example="40%"),
     *                                 @OA\Property(property="type", type="string", example="Percentage"),
     *                                 @OA\Property(property="per_student", type="integer", example=1)
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show($id)
    {
        // Fetching the Counselor Referrer data along with related target groups, academic year, and targets
        $data = CounselorReferrer::with([
            'targetGroups.academicYear',
            'targetGroups.targets',
            'createds',
            'updatedBy'
        ])->findOrFail($id);

        // Formatting the response data
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
            'target_groups' => $data->targetGroups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'academic_year_id' => $group->academic_year_id,
                    'academic_year_name' => $group->academicYear ? $group->academicYear->name : null,
                    'targets' => $group->targets->map(function ($target) {
                        return [
                            'id' => $target->id,
                            'min_target' => $target->min_target,
                            'max_target' => $target->max_target,
                            'amount_percentage' => $target->amount_percentage,
                            'type' => $target->type,
                            'per_student' => $target->per_student,
                        ];
                    }),
                ];
            }),
        ];

        // Return the formatted response in JSON
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
