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
    public function index()
    {
        $counselor = CounselorReferrer::all();
        if ($counselor->isEmpty()) {
            return response()->json([
                'message' => 'No Counselor Referral found',
                'status' => 0,
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Counselor Referral retrieved successfully',
            'status' => 1,
            'data' => $counselor
        ], 200);
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
        $data = CounselorReferrer::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message' => 'Counselor Referral not found',
                'status' => 0,
            ];
        } else {
            $response = [
                'message' => 'Counselor Referral found',
                'status' => 1,
                'data' => $data
            ];
        }
        return response()->json($response, 200);
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
}
