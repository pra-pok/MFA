<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralSource;
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

class ReferralSourceRestApiController extends Controller
{

    /**
     * Get List Referral Source
     * @OA\Get (
     *     path="/api/v1/referral/source/",
     *     security={{"Bearer": {}}},
     *     tags={"Referral Source"},
     *     summary="Retrieve a list of counselor Source",
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
     *                         property="title",
     *                         type="string",
     *                         example="example title"
     *                     ),
     *                     @OA\Property(
     *                         property="status",
     *                         type="boolean",
     *                         example="1"
     *                     ),
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
        $statuses = ReferralSource::all();
        if ($statuses->isEmpty()) {
            return response()->json([
                'message' => 'No Referral Source found',
                'status' => 0,
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Referral Source retrieved successfully',
            'status' => 1,
            'data' => $statuses
        ], 200);
    }
    /**
     * Store Referral Source
     * @OA\Post (
     *     path="/api/v1/referral/source/store",
     *     security={{"Bearer": {}}},
     *     tags={"Referral Source"},
     *     summary="Create a new counselor Source",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="title",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="content",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "title":"example title",
     *                     "status":"example status",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="title", type="string", example="title"),
     *              @OA\Property(property="status", type="boolean", example="1"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string", example="fail"),
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
            ]);
            $data = ReferralSource::create($validatedData);
            return response()->json([
                'message' => 'Referral Source added successfully',
                'data'    => $data
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function edit($id)
    {
    }
    /**
     * Update Referral Source
     * @OA\Put (
     *     path="/api/v1/referral/source/update/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Referral Source"},
     *     summary="Update an existing Counselor Source",
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
     *                          property="title",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="content",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "title":"example title",
     *                     "status":"example status",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="title", type="string", example="title"),
     *              @OA\Property(property="status", type="boolean", example="1"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
        ]);
        $data = ReferralSource::find($id);
        if (!$data) {
            return response()->json([
                'message' => 'Referral Source not found',
                'status'  => 0
            ], 404);
        }
        DB::beginTransaction();
        try {
            $data->update($validatedData);
            DB::commit();
            return response()->json([
                'message' => 'Referral Source updated successfully',
                'status'  => 1,
                'data'    => $data
            ], 200);
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Internal Server Error: ' . $err->getMessage(),
                'status'  => 0
            ], 500);
        }
    }

    /**
     * Delete Referral Source
     * @OA\Delete (
     *     path="/api/v1/referral/source/delete/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Referral Source"},
     *     summary="Delete a Counselor Source",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="delete todo success")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $id)
    {
        $data = ReferralSource::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message'    => 'Referral Source does not exits',
                'status'     => 0,
            ];
            $respCode    = 404;
        } else {
            DB::beginTransaction();
            try {
                $data->delete();
                DB::commit();
                $response = [
                    'message'  => 'Referral Source deleted successfully',
                    'status'     => 1,
                ];
                $respCode    = 200;
            } catch (\Exception $err) {
                DB::rollBack();
                $response = [
                    'message'  => 'Internal Server error',
                    'status'     => 0,
                ];
                $respCode    = 500;
            }
        }
        return response()->json($response, $respCode);
    }
    /**
     * @OA\Get(
     *     path="/api/v1/referral/source/show/{id}",
     *      security={{"Bearer": {}}},
     *      tags={"Referral Source"},
     *      summary="Get single  Referral Source",
     *      description="Returns list of Referral Source",
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
        $data = ReferralSource::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message'          => 'Referral Source not found',
                'status'           => 0,
            ];
        } else {
            $response = [
                'message'       => 'Referral Source found',
                'status'        => 1,
                'data'          =>  $data
            ];
        }
        return response()->json($response, 200);
    }
}
