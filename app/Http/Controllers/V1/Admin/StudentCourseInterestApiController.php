<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentCourseInterest;

class StudentCourseInterestApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/students/course/interests",
     *     security={{"Bearer": {}}},
     *     summary="Get all Student Course Interest",
     *     description="Retrieve a list of all student education history records.",
     *     tags={"Student Course Interest"},
     *     @OA\Response(
     *         response=200,
     *         description="List of student course interests retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *            type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="student_id", type="integer"),
     *                 @OA\Property(property="course_id", type="integer"),
     *                 @OA\Property(property="agreement_amount", type="number", format="float"),
     *                 @OA\Property(property="remarks", type="string", nullable=true),
     *                 @OA\Property(property="current", type="boolean"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No education history records found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="No records found"),
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Something went wrong"),
     *             @OA\Property(property="status", type="integer", example=0)
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $courseInterests = StudentCourseInterest::all();
            if ($courseInterests->isEmpty()) {
                return response()->json([
                    'message' => 'No student course interests found',
                    'status' => 0,
                    'data' => []
                ], 404);
            }

            return response()->json([
                'message' => 'Student course interests retrieved successfully',
                'status' => 1,
                'data' => $courseInterests
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error',
                'status' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/students/course/interests",
     *     security={{"Bearer": {}}},
     *     summary="Create a New Student Course Interest",
     *     description="Stores a new course interest for a student.",
     *     tags={"Student Course Interest"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"student_id", "course_id", "agreement_amount"},
     *             @OA\Property(property="student_id", type="integer", example=1),
     *             @OA\Property(property="course_id", type="integer", example=2),
     *             @OA\Property(property="agreement_amount", type="number", format="float", example=1000.50),
     *             @OA\Property(property="remarks", type="string", nullable=true, example="Interested in advanced training"),
     *             @OA\Property(property="current", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student course interest created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student course interest created successfully"),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="student_id", type="integer"),
     *                 @OA\Property(property="course_id", type="integer"),
     *                 @OA\Property(property="agreement_amount", type="number", format="float"),
     *                 @OA\Property(property="remarks", type="string", nullable=true),
     *                 @OA\Property(property="current", type="boolean"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Server error"),
     *             @OA\Property(property="status", type="integer", example=0)
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'student_id' => 'required|exists:students,id',
                'course_id' => 'required|exists:courses,id',
                'agreement_amount' => 'required|numeric|min:0',
                'remarks' => 'nullable|string',
                'current' => 'boolean'
            ]);

            // Create student course interest record
            $courseInterest = StudentCourseInterest::create($validatedData);

            return response()->json([
                'message' => 'Student course interest created successfully',
                'status' => 1,
                'data' => $courseInterest
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'status' => 0,
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error',
                'status' => 0,
                'error' => $e->getMessage()
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
     *     path="/api/v1/students/course/interests/{id}",
     *     security={{"Bearer": {}}},
     *     summary="Update an Existing Student Course Interest",
     *     description="Updates an existing student course interest record.",
     *     tags={"Student Course Interest"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the student course interest to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"student_id", "course_id", "agreement_amount"},
     *             @OA\Property(property="student_id", type="integer", example=1),
     *             @OA\Property(property="course_id", type="integer", example=2),
     *             @OA\Property(property="agreement_amount", type="number", format="float", example=1500.75),
     *             @OA\Property(property="remarks", type="string", nullable=true, example="Updated remarks"),
     *             @OA\Property(property="current", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student course interest updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student course interest updated successfully"),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="student_id", type="integer"),
     *                 @OA\Property(property="course_id", type="integer"),
     *                 @OA\Property(property="agreement_amount", type="number", format="float"),
     *                 @OA\Property(property="remarks", type="string", nullable=true),
     *                 @OA\Property(property="current", type="boolean"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student course interest not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Record not found"),
     *             @OA\Property(property="status", type="integer", example=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Server error"),
     *             @OA\Property(property="status", type="integer", example=0)
     *         )
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'student_id' => 'required|exists:students,id',
                'course_id' => 'required|exists:courses,id',
                'agreement_amount' => 'required|numeric|min:0',
                'remarks' => 'nullable|string',
                'current' => 'boolean'
            ]);
            $courseInterest = StudentCourseInterest::find($id);

            if (!$courseInterest) {
                return response()->json([
                    'message' => 'Record not found',
                    'status' => 0
                ], 404);
            }
            $courseInterest->update($validatedData);
            return response()->json([
                'message' => 'Student course interest updated successfully',
                'status' => 1,
                'data' => $courseInterest
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'status' => 0,
                'errors' => $e->errors()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error',
                'status' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/students/course/interests/{id}",
     *     security={{"Bearer": {}}},
     *     summary="Delete a Student Course Interest",
     *     description="Soft deletes a student course interest record.",
     *     tags={"Student Course Interest"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the student course interest to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student course interest deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student course interest deleted successfully"),
     *             @OA\Property(property="status", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student course interest not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Record not found"),
     *             @OA\Property(property="status", type="integer", example=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Server error"),
     *             @OA\Property(property="status", type="integer", example=0)
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $courseInterest = StudentCourseInterest::find($id);
            if (!$courseInterest) {
                return response()->json([
                    'message' => 'Record not found',
                    'status' => 0
                ], 404);
            }
            $courseInterest->delete();

            return response()->json([
                'message' => 'Student course interest deleted successfully',
                'status' => 1
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server error',
                'status' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
