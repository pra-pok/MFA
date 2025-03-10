<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentEducationHistory;
use Exception;
use Illuminate\Support\Facades\Validator;

class StudentEducationHistoryApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/students/education/history",
     *     security={{"Bearer": {}}},
     *     summary="Get All Education History for a Student",
     *     description="Returns a list of all education history records for a specific student.",
     *     tags={"Student Education History"},
     *     @OA\Parameter(
     *         name="student_id",
     *         in="path",
     *         description="ID of the student whose education history is to be retrieved",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of all education history records for the student",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="address", type="string", nullable=true),
     *                 @OA\Property(property="marks_received", type="string", nullable=true),
     *                 @OA\Property(property="note", type="string", nullable=true),
     *                 @OA\Property(property="course_studied", type="string"),
     *                 @OA\Property(property="student_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No education history found for this student",
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
    public function index($student_id)
    {
        try {
            $educationHistory = StudentEducationHistory::withoutTrashed()->get();

            if ($educationHistory->isEmpty()) {
                return response()->json([
                    'message' => 'No education history found for this student',
                    'status' => 0,
                    'data' => []
                ], 404);
            }

            return response()->json([
                'message' => 'Education history retrieved successfully',
                'status' => 1,
                'data' => $educationHistory
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
     *     path="/api/v1/students/education/history",
     *     security={{"Bearer": {}}},
     *     summary="Create new Education History for a Student",
     *     description="Creates a new education history record for a specific student.",
     *     tags={"Student Education History"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for the new education history record",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "course_studied", "marks_received"},
     *             @OA\Property(property="name", type="string", example="XYZ University"),
     *             @OA\Property(property="address", type="string", nullable=true, example="123 University St"),
     *             @OA\Property(property="marks_received", type="string", example="85%"),
     *             @OA\Property(property="note", type="string", nullable=true, example="Excellent performance"),
     *             @OA\Property(property="course_studied", type="string", example="Computer Science"),
     *             @OA\Property(property="student_id", type="integer", example="5")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Education history created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Education history created successfully"),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="address", type="string", nullable=true),
     *                 @OA\Property(property="marks_received", type="string"),
     *                 @OA\Property(property="note", type="string", nullable=true),
     *                 @OA\Property(property="course_studied", type="string"),
     *                 @OA\Property(property="student_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation error: 'course_studied' is required."),
     *             @OA\Property(property="status", type="integer", example=0)
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
                'course_studied' => 'required|string|max:255',
                'marks_received' => 'required|string|max:100',
                'note' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'student_id' => 'required|exists:students,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation error: ' . $validator->errors()->first(),
                    'status' => 0
                ], 400);
            }

            $educationHistory = StudentEducationHistory::create($request->all());

            return response()->json([
                'message' => 'Student education history created successfully',
                'status' => 1,
                'data' => $educationHistory
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
     *     path="/api/v1/students/education/history/{id}",
     *     security={{"Bearer": {}}},
     *     summary="Update Education History for a Student",
     *     description="Updates an existing education history record for a specific student.",
     *     tags={"Student Education History"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated data for the education history record",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "course_studied", "marks_received"},
     *             @OA\Property(property="name", type="string", example="XYZ University"),
     *             @OA\Property(property="address", type="string", nullable=true, example="123 University St"),
     *             @OA\Property(property="marks_received", type="string", example="85%"),
     *             @OA\Property(property="note", type="string", nullable=true, example="Excellent performance"),
     *             @OA\Property(property="course_studied", type="string", example="Computer Science"),
     *             @OA\Property(property="student_id", type="integer", example="5")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Education history updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Education history updated successfully"),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="address", type="string", nullable=true),
     *                 @OA\Property(property="marks_received", type="string"),
     *                 @OA\Property(property="note", type="string", nullable=true),
     *                 @OA\Property(property="course_studied", type="string"),
     *                 @OA\Property(property="student_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation error: 'course_studied' is required."),
     *             @OA\Property(property="status", type="integer", example=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Education history or student not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Education history or student not found."),
     *             @OA\Property(property="status", type="integer", example=0)
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

    public function update(Request $request, string $id)
    {
        try {
            $educationHistory = StudentEducationHistory::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'course_studied' => 'required|string|max:255',
                'marks_received' => 'required|string|max:100',
                'note' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'student_id' => 'required|exists:students,id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 400);
            }

            $educationHistory->update($request->all());
            return response()->json([
                'message' => 'Student education history updated successfully',
                'status' => 1,
                'data' => $educationHistory
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
     *     path="/api/v1/students/education/history/{id}",
     *     security={{"Bearer": {}}},
     *     summary="Delete Education History for a Student",
     *     description="Deletes a student education history from the system.",
     *     tags={"Student Education History"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student education history deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student education history not found",
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

            $educationHistory = StudentEducationHistory::findOrFail($id);
            $educationHistory->delete();
            return response()->json([
                'message' => 'Student education history deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()

            ], 500);
        }
    }
}
