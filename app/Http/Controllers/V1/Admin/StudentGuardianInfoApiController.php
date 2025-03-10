<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentGuardianInfo;
use Exception;
use Illuminate\Support\Facades\Validator;

class StudentGuardianInfoApiController extends Controller
{

/**
 * @OA\Get(
 *     path="/api/v1/students/guardians/info",
 *     security={{"Bearer": {}}},
 *     summary="Get All Student Guardians",
 *     description="Returns a list of all student guardians.",
 *     tags={"Student Guardian Info"},
 *     @OA\Response(
 *         response=200,
 *         description="List of all student guardians",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="phone", type="string", nullable=true),
 *                 @OA\Property(property="address", type="string", nullable=true),
 *                 @OA\Property(property="type", type="string", nullable=true),
 *                 @OA\Property(property="student_id", type="integer"),
 *                 @OA\Property(property="current_guardian", type="boolean"),
 *                 @OA\Property(property="deleted_at", type="string", nullable=true),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No student guardians found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer")
 *         )
 *     )
 * )
 */
    public function index()
    {
        try {
            $guardians = StudentGuardianInfo::withoutTrashed()->get();
            if ($guardians->isEmpty()) {
                return response()->json([
                    'message' => 'No guardians found',
                    'status' => 0,
                    'data' => []
                ], 404);
            }
    
            return response()->json([
                'message' => 'Guardians retrieved successfully',
                'status' => 1,
                'data' => $guardians
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'status' => 0
            ], 500);
        }
    }

/**
 * @OA\Post(
 *     path="/api/v1/students/guardians/info",
 *     security={{"Bearer": {}}},
 *     summary="Create a New Guardian",
 *     description="Stores a new guardian for a student.",
 *     tags={"Student Guardian Info"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "student_id"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="phone", type="string", nullable=true, example="1234567890"),
 *             @OA\Property(property="address", type="string", nullable=true, example="123 Street, City"),
 *             @OA\Property(property="type", type="string", nullable=true, example="Father"),
 *             @OA\Property(property="student_id", type="integer", example=5),
 *             @OA\Property(property="current_guardian", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Guardian created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer")
 *         )
 *     )
 * )
 */

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:50',
                'student_id' => 'required|exists:students,id',
                'current_guardian' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $guardian = StudentGuardianInfo::create($request->all());
    
            return response()->json([
                'message' => 'Guardian created successfully',
                'status' => 1,
                'data' => $guardian
            ], 201);
    
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'status' => 0
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

/**
 * @OA\Put(
 *     path="/api/v1/students/guardians/info/{id}",
 *     security={{"Bearer": {}}},
 *     summary="Update Guardian",
 *     description="Updates an existing guardian for a student.",
 *     tags={"Student Guardian Info"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "student_id"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="phone", type="string", nullable=true, example="1234567890"),
 *             @OA\Property(property="address", type="string", nullable=true, example="123 Street, City"),
 *             @OA\Property(property="type", type="string", nullable=true, example="Father"),
 *             @OA\Property(property="student_id", type="integer", example=5),
 *             @OA\Property(property="current_guardian", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Guardian updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Guardian not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation failed",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer"),
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer")
 *         )
 *     )
 * )
 */
 
    public function update(Request $request,$id)
    {
        try {
            $guardian = StudentGuardianInfo::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:50',
                'student_id' => 'required|exists:students,id',
                'current_guardian' => 'boolean'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 400);
            }

            $guardian->update($request->all());
            return response()->json([
                'message' => 'Guardian updated successfully',
                'status' => 1,
                'data' => $guardian
            ], 200);
    
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'status' => 0
            ], 500);
        }
    }

/**
 * @OA\Delete(
 *     path="/api/v1/students/guardians/info/{id}",
 *     security={{"Bearer": {}}},
 *     summary="Delete Guardian",
 *     description="Deletes a guardian from the system.",
 *     tags={"Student Guardian Info"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Guardian deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Guardian not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="status", type="integer")
 *         )
 *     )
 * )
 */
    public function destroy($id)
    {
        try {
            
            $guardian = StudentGuardianInfo::findOrFail($id);
            $guardian->delete();
            return response()->json([
                'message' => 'Guardian deleted successfully',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            
            ], 500);
        }
    }
}
