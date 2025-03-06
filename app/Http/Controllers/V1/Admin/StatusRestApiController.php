<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CounsellingStatus;
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

class StatusRestApiController extends Controller
{

    /**
     * Get List Status
     * @OA\Get (
     *     path="/api/v1/status/",
     *     security={{"Bearer": {}}},
     *     tags={"Status"},
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
     *                         property="color",
     *                         type="string",
     *                         example="#000000"
     *                     ),
     *                      @OA\Property(
     *                         property="note",
     *                         type="text",
     *                         example="example note"
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
        $statuses = CounsellingStatus::all();
        if ($statuses->isEmpty()) {
            return response()->json([
                'message' => 'No statuses found',
                'status' => 0,
                'data' => []
            ], 404);
        }

        return response()->json([
            'message' => 'Statuses retrieved successfully',
            'status' => 1,
            'data' => $statuses
        ], 200);
    }
    /**
     * Store Status
     * @OA\Post (
     *     path="/api/v1/status/store",
     *     security={{"Bearer": {}}},
     *     tags={"Status"},
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
     *                     "color":"example color",
     *                     "note":"example note",
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
     *              @OA\Property(property="color", type="string", example="color"),
     *              @OA\Property(property="note", type="text", example="note"),
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
                'color' => 'required|string|max:50',
                'note'  => 'nullable|string'
            ]);
            $data = CounsellingStatus::create($validatedData);
            return response()->json([
                'message' => 'Status added successfully',
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
     * Update Status
     * @OA\Put (
     *     path="/api/vi/status/update/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Status"},
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
     *                     "color":"example color",
     *                    "note":"example note",
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
     *              @OA\Property(property="color", type="string", example="color"),
     *             @OA\Property(property="note", type="text", example="note"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id)
    {
        $data = CounsellingStatus::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message'    => 'Status does not exits',
                'status'     => 0,
            ];
            $respCode    = 404;
        } else {
            DB::beginTransaction();
            try {
                $request->validate(CounsellingStatus::getRules());
                $data->title                   = $request->title;
                $data->color                    = $request->color;
                $data->note                  = $request->note;
                $data->save();
                DB::commit();
                $response = [
                    'message'  => 'Status updated successfully',
                    'status'     => 1,
                    'data'      =>  $data
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
     * Delete Status
     * @OA\Delete (
     *     path="/api/v1/status/delete/{id}",
     *     security={{"Bearer": {}}},
     *     tags={"Status"},
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
        $data = CounsellingStatus::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message'    => 'Status does not exits',
                'status'     => 0,
            ];
            $respCode    = 404;
        } else {
            DB::beginTransaction();
            try {
                $data->delete();
                DB::commit();
                $response = [
                    'message'  => 'Status deleted successfully',
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
     *     path="/api/v1/status/show/{id}",
     *      security={{"Bearer": {}}},
     *      tags={"Status"},
     *      summary="Get single  status",
     *      description="Returns list of status",
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
     *
     * Returns list of books
     */
    public function show($id)
    {
        $data = CounsellingStatus::findOrFail($id);
        if (is_null($data)) {
            $response = [
                'message'          => 'Status not found',
                'status'           => 0,
            ];
        } else {
            $response = [
                'message'       => 'Status found',
                'status'        => 1,
                'data'          =>  $data
            ];
        }
        return response()->json($response, 200);
    }
}
