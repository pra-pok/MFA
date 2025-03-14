<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentCounselorReffer;
use App\Models\CounselorReferrer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use DB;
use Exception;

class StudentApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/students",
     *     summary="Get list of students",
     *     tags={"Students"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of students per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="A list of Students",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Students retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                     @OA\Property(property="address", type="string", example="123 Main St"),
     *                     @OA\Property(property="phone", type="string", example="+1234567890"),
     *                     @OA\Property(property="counselors", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="counselor_name", type="string", example="Jane Smith"),
     *                             @OA\Property(property="counselor_email", type="string", example="jane.smith@example.com"),
     *                             @OA\Property(property="counselor_role_name", type="string", example="Senior Counselor")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No Student found"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $students = Student::with('counselors')->paginate($perPage);

        if ($students->isEmpty()) {
            return response()->json([
                'message' => 'No Student found',
                'status' => 0,
                'data' => [],
                'pagination' => null
            ], 404);
        }

        return response()->json([
            'message' => 'Students retrieved successfully',
            'status' => 1,
            'data' => $students->items(),
            'pagination' => [
                'total' => $students->total(),
                'per_page' => $students->perPage(),
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage()
            ]
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
     *             @OA\Property(
     *                 property="counselor_referred_id",
     *                 type="array",
     *                 description="Array of counselor/referrer/agent IDs",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Student successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="address", type="string", example="123 Main St"),
     *                 @OA\Property(property="phone", type="string", example="+1234567890"),
     *                 @OA\Property(property="permanent_address", type="string", example="Permanent Address Example"),
     *                 @OA\Property(property="temporary_address", type="string", example="Temporary Address Example"),
     *                 @OA\Property(property="permanent_locality_id", type="integer", example=101),
     *                 @OA\Property(property="temporary_locality_id", type="integer", example=102),
     *                 @OA\Property(property="referral_source_id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="counselor_referred_id",
     *                     type="array",
     *                     @OA\Items(type="integer", example=2)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation failed"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal Server Error"
     *     )
     * )
     */

    public function store(Request $request)
    {
        DB::beginTransaction();

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
                'counselor_referred_id' => 'required|array|min:1',
                'counselor_referred_id.*' => 'exists:counselor_referrers,id'
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
            ]);
            $counselorReferrers = [];
            foreach ($validated['counselor_referred_id'] as $counselorReferrerId) {
                $counselor = CounselorReferrer::find($counselorReferrerId);

                if ($counselor) {
                    $counselorReferrers[] = [
                        'student_id' => $student->id,
                        'counselor_referred_id' => $counselor->id,
                        'student_name' => $student->name,
                        'student_email' => $student->email,
                        'student_phone' => $student->phone,
                        'counselor_name' => $counselor->name,
                        'counselor_email' => $counselor->email,
                        'counselor_role_name' => $counselor->role,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            StudentCounselorReffer::insert($counselorReferrers);

            DB::commit();

            return response()->json([
                'message' => 'Student and counselors saved successfully',
                'data' => Student::with('counselors')->find($student->id)
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

    /**
     * @OA\Put(
     *     path="/api/v1/students/{id}",
     *     summary="Update a student",
     *     tags={"Students"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the student to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "address", "phone"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *             @OA\Property(property="address", type="string", example="123 Main St"),
     *             @OA\Property(property="phone", type="string", example="+1234567890"),
     *             @OA\Property(property="permanent_address", type="string", example="Permanent Address Example"),
     *             @OA\Property(property="temporary_address", type="string", example="Temporary Address Example"),
     *             @OA\Property(property="permanent_locality_id", type="integer", example=101),
     *             @OA\Property(property="temporary_locality_id", type="integer", example=102),
     *             @OA\Property(property="referral_source_id", type="integer", example=1),
     *             @OA\Property(property="counselor_referred_id", type="array",
     *                 @OA\Items(type="integer", example=2)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Student updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="address", type="string", example="123 Main St"),
     *                 @OA\Property(property="phone", type="string", example="+1234567890"),
     *                 @OA\Property(property="permanent_address", type="string", example="Permanent Address Example"),
     *                 @OA\Property(property="temporary_address", type="string", example="Temporary Address Example"),
     *                 @OA\Property(property="permanent_locality_id", type="integer", example=101),
     *                 @OA\Property(property="temporary_locality_id", type="integer", example=102),
     *                 @OA\Property(property="referral_source_id", type="integer", example=1),
     *                 @OA\Property(property="counselors", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="counselor_name", type="string", example="Jane Smith"),
     *                         @OA\Property(property="counselor_email", type="string", example="jane.smith@example.com"),
     *                         @OA\Property(property="counselor_role_name", type="string", example="Senior Counselor")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Student not found"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email,' . $id,
                'address' => 'required|string',
                'phone' => 'required|string|max:15',
                'permanent_address' => 'nullable|string',
                'temporary_address' => 'nullable|string',
                'permanent_locality_id' => 'nullable|integer',
                'temporary_locality_id' => 'nullable|integer',
                'referral_source_id' => 'nullable|integer',
                'counselor_referred_id' => 'required|array|min:1',
                'counselor_referred_id.*' => 'exists:counselor_referrers,id'
            ]);

            // Update student details
            $student->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'permanent_address' => $validated['permanent_address'] ?? null,
                'temporary_address' => $validated['temporary_address'] ?? null,
                'permanent_locality_id' => $validated['permanent_locality_id'] ?? null,
                'temporary_locality_id' => $validated['temporary_locality_id'] ?? null,
                'referral_source_id' => $validated['referral_source_id'] ?? null,
            ]);

            // Delete existing counselor references
            StudentCounselorReffer::where('student_id', $student->id)->delete();

            // Insert updated counselor references
            $counselorReferrers = [];
            foreach ($request->counselor_referred_id as $counselorReferrerId) {
                $counselor = CounselorReferrer::find($counselorReferrerId);

                $counselorReferrers[] = [
                    'student_id' => $student->id,
                    'counselor_referred_id' => $counselorReferrerId,
                    'student_name' => $student->name,
                    'student_email' => $student->email,
                    'student_phone' => $student->phone,
                    'counselor_name' => $counselor->name ?? null,
                    'counselor_email' => $counselor->email ?? null,
                    'counselor_role_name' => $counselor->role ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            StudentCounselorReffer::insert($counselorReferrers);

            return response()->json([
                'message' => 'Student updated successfully',
                'data' => $student->load('counselors')
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found'
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
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
    /**
     * @OA\Get(
     *     path="/api/v1/student/list",
     *     summary="Get list of student",
     *     tags={"Config Search"},
     *     security={{"Bearer": {}}},
     *     description="Returns list of student",
     *
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful response - List of Status",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="example title"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Internal Server Error"),
     *             @OA\Property(property="error", type="string", example="Exception message here")
     *         )
     *     )
     * )
     */
    public function getStudent()
    {
        try {
            $students = Student::orderBy('created_at', 'desc')->get()
                ->makeHidden([
                    'permanent_address', 'created_at', 'updated_at', 'temporary_address',
                    'permanent_locality_id', 'temporary_locality_id', 'referral_source_id', 'deleted_at'
                ]);
            return response()->json([
                'message' => '',
                'data' => $students
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
