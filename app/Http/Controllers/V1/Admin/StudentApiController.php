<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;


class StudentApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/students",
     *     summary="Get list of students",
     *     tags={"Students"},
     *     security={{"Bearer": {}}},
     *     @OA\Response(
     *         response="200",
     *         description="A list of Students",
     *         @OA\JsonContent(type="array", 
     *             @OA\Items(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="name", type="string", description="Student's full name"),
     *                     @OA\Property(property="email", type="string", description="Student's email address"),
     *                     @OA\Property(property="address", type="string", description="Student's address"),
     *                     @OA\Property(property="phone", type="string", description="Student's phone number"),
     *                     @OA\Property(property="permanent_address", type="string", description="Permanent address of student"),
     *                     @OA\Property(property="temporary_address", type="string", description="Temporary address of student"),
     *                     @OA\Property(property="permanent_locality_id", type="integer", description="ID of the locality for the permanent address"),
     *                     @OA\Property(property="temporary_locality_id", type="integer", description="ID of the locality for the temporary address"),
     *                     @OA\Property(property="referral_source_id", type="integer", description="ID of the referral source"),
     *                     @OA\Property(property="counselor_referred_id", type="integer", description="ID of the counselor who referred the student")
     *                 }
     *             )
     *         )
     *     )
     * )
     */

     public function index(){
        $students = Student::all();

         if ($students->isEmpty()) {
            return response()->json([
                'message' => 'No Student found',
                'status' => 0,
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Student retrieved successfully',
            'status' => 1,
            'data' => $students
        ], 200);
     }

    /**
    * @OA\Post(
    *     path="/api/v1/students",
    *     summary="Create a new student",
    *     security={{"Bearer": {}}},
    *     tags={"Students"},
    *     @OA\RequestBody(
    *         required=true,
    *         @OA\JsonContent(
    *             required={"name", "email", "address", "phone"},
    *             @OA\Property(property="name", type="string", description="Student's full name"),
    *             @OA\Property(property="email", type="string", description="Student's email address"),
    *             @OA\Property(property="address", type="string", description="Student's address"),
    *             @OA\Property(property="phone", type="string", description="Student's phone number"),
    *             @OA\Property(property="permanent_address", type="string", description="Permanent address of student"),
    *             @OA\Property(property="temporary_address", type="string", description="Temporary address of student"),
    *             @OA\Property(property="permanent_locality_id", type="integer", description="ID of the locality for the permanent address"),
    *             @OA\Property(property="temporary_locality_id", type="integer", description="ID of the locality for the temporary address"),
    *             @OA\Property(property="referral_source_id", type="integer", description="ID of the referral source"),
    *             @OA\Property(property="counselor_referred_id", type="integer", description="ID of the counselor who referred the student")
    *         )
    *     ),
    *     @OA\Response(
    *         response="200",
    *         description="Student successfully created",
    *         @OA\JsonContent(
    *             @OA\Property(property="id", type="integer", example=1),
    *             @OA\Property(property="name", type="string", example="John Doe"),
    *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
    *             @OA\Property(property="address", type="string", example="123 Main St"),
    *             @OA\Property(property="phone", type="string", example="+1234567890"),
    *             @OA\Property(property="permanent_address", type="string", example="Permanent Address Example"),
    *             @OA\Property(property="temporary_address", type="string", example="Temporary Address Example"),
    *             @OA\Property(property="permanent_locality_id", type="integer", example=101),
    *             @OA\Property(property="temporary_locality_id", type="integer", example=102),
    *             @OA\Property(property="referral_source_id", type="integer", example=1),
    *             @OA\Property(property="counselor_referred_id", type="integer", example=2),
    *             @OA\Property(property="created_at", type="string", format="date-time", example="2021-12-11T09:25:53.000000Z"),
    *             @OA\Property(property="updated_at", type="string", format="date-time", example="2021-12-11T09:25:53.000000Z")
    *         )
    *     ),
    *     @OA\Response(
    *         response="400",
    *         description="Invalid input"
    *     )
    * )
    */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email',
                'address' => 'required|string',
                'phone' => 'required|string|max:15',
                'permanent_address' => 'nullable|string',
                'temporary_address' => 'nullable|string',
                'permanent_locality_id' => 'nullable|integer',
                'temporary_locality_id' => 'nullable|integer',
                'referral_source_id' => 'nullable|integer',
                'counselor_referred_id' => 'nullable|integer',
            ]);

            $student = Student::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'permanent_address' => $validated['permanent_address'] ?? null,
                'temporary_address' => $validated['temporary_address'] ?? null,
                'permanent_locality_id' => $validated['permanent_locality_id'] ?? null,
                'temporary_locality_id' => $validated['temporary_locality_id'] ?? null,
                'referral_source_id' => $validated['referral_source_id'] ?? null,
                'counselor_referred_id' => $validated['counselor_referred_id'] ?? null,
            ]);

            return response()->json([
                'message' => 'Student created successfully',
                'data'    => $student
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

/**
 * @OA\Put(
 *     path="/api/v1/students/{id}",
 *     summary="Update an existing student",
 *     tags={"Students"},
 *     security={{"Bearer": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the student to be updated",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "address", "phone"},
 *             @OA\Property(property="name", type="string", description="Student's full name", example="Prakash Pokhrel"),
 *             @OA\Property(property="email", type="string", description="Student's email address", example="prakash.pokhrel@example.com"),
 *             @OA\Property(property="address", type="string", description="Student's address", example="Pokhara"),
 *             @OA\Property(property="phone", type="string", description="Student's phone number", example="9866064728"),
 *             @OA\Property(property="permanent_address", type="string", description="Permanent address of student", example="Nepaljung"),
 *             @OA\Property(property="temporary_address", type="string", description="Temporary address of student", example="Pokhara"),
 *             @OA\Property(property="permanent_locality_id", type="integer", description="ID of the locality for the permanent address", example=1),
 *             @OA\Property(property="temporary_locality_id", type="integer", description="ID of the locality for the temporary address", example=1),
 *             @OA\Property(property="referral_source_id", type="integer", description="ID of the referral source", example=1),
 *             @OA\Property(property="counselor_referred_id", type="integer", description="ID of the counselor who referred the student", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Student updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Student updated successfully"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Prakash Pokhrel"),
 *                 @OA\Property(property="email", type="string", example="prakash.pokhrel@example.com"),
 *                 @OA\Property(property="address", type="string", example="Pokhara"),
 *                 @OA\Property(property="phone", type="string", example="9866064728"),
 *                 @OA\Property(property="permanent_address", type="string", example="Nepaljung"),
 *                 @OA\Property(property="temporary_address", type="string", example="Pokhara"),
 *                 @OA\Property(property="permanent_locality_id", type="integer", example=1),
 *                 @OA\Property(property="temporary_locality_id", type="integer", example=1),
 *                 @OA\Property(property="referral_source_id", type="integer", example=1),
 *                 @OA\Property(property="counselor_referred_id", type="integer", example=1),
 *                 @OA\Property(property="created_at", type="string", example="2025-03-09T10:19:39.000000Z"),
 *                 @OA\Property(property="updated_at", type="string", example="2025-03-09T10:19:39.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response="422",
 *         description="Validation failed",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validation failed"),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
 *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response="500",
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Internal Server Error"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'email@example.com' for key 'students_email_unique'")
 *         )
 *     )
 * )
 */
    public function update(Request $request, $id)
    {  
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'address' => 'required|string',
            'phone' => 'required|string|max:15',
            'permanent_address' => 'nullable|string',
            'temporary_address' => 'nullable|string',
            'permanent_locality_id' => 'nullable|integer',
            'temporary_locality_id' => 'nullable|integer',
            'referral_source_id' => 'nullable|integer',
            'counselor_referred_id' => 'nullable|integer',
        ]);

        $students=Student::findOrFail($id); 

          $students->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'phone' => $validated['phone'],
            'permanent_address' => $validated['permanent_address'] ?? null,
            'temporary_address' => $validated['temporary_address'] ?? null,
            'permanent_locality_id' => $validated['permanent_locality_id'] ?? null,
            'temporary_locality_id' => $validated['temporary_locality_id'] ?? null,
            'referral_source_id' => $validated['referral_source_id'] ?? null,
            'counselor_referred_id' => $validated['counselor_referred_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'Student updated successfully',
            'data'    => $students
        ], 200);

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
    /**
 * @OA\Delete(
 *     path="/api/v1/students/{id}",
 *     summary="Delete a student",
 *     tags={"Students"},
 *     security={{"Bearer": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the student to be deleted",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Student deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Student deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Student not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Student not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response="500",
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Internal Server Error"),
 *             @OA\Property(property="error", type="string", example="SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row")
 *         )
 *     )
 * )
 */

    public function destroy($id)
   {
    try {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);
        }
        $student->delete();
        return response()->json([
            'message' => 'Student deleted successfully'
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
