<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentCounselorReffer;
use App\Models\CounselorReferrer;
use App\Models\StudentCourseInterest;
use App\Models\StudentEducationHistory;
use App\Models\StudentGuardianInfo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Exception;

class StudentSinglePageRestApiController extends Controller
{

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
     *     path="/api/v1/single/page/students",
     *     summary="Create a new student",
     *     security={{"Bearer": {}}},
     *     tags={"Students"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "address", "phone", "counselor_referred_id"},
     *             @OA\Property(property="name", type="string", description="Student's full name"),
     *             @OA\Property(property="email", type="string", format="email", description="Student's email address"),
     *             @OA\Property(property="address", type="string", description="Student's address"),
     *             @OA\Property(property="phone", type="string", description="Student's phone number"),
     *             @OA\Property(property="permanent_address", type="string", description="Permanent address of student"),
     *             @OA\Property(property="temporary_address", type="string", description="Temporary address of student"),
     *             @OA\Property(property="permanent_locality_id", type="integer", description="ID of the locality for the permanent address"),
     *             @OA\Property(property="referral_source_id", type="integer", description="ID of the referral source"),
     *             @OA\Property(
     *                 property="counselor_referred_id",
     *                  type="array",
     *                 description="Array of counselor IDs or new counselor data",
     *                @OA\Items(
     *                     anyOf={
     *                         @OA\Schema(type="integer", description="Existing counselor ID"),
     *                        @OA\Schema(type="object", description="New counselor details",
     *                             @OA\Property(property="name", type="string", description="Name of the counselor"),
     *                             @OA\Property(property="email", type="string", description="Email of the counselor"),
     *                             @OA\Property(property="phone", type="string", description="Phone number of the counselor"),
     *                            @OA\Property(property="role", type="string", description="Role of the counselor")
     *                        )
     *                    }
     *                 )
     *             ),
     *             @OA\Property(property="course_info", type="array", description="Courses interested in",
     *                 @OA\Items(
     *                     @OA\Property(property="course_id", type="integer", description="ID of the course"),
     *                     @OA\Property(property="remarks", type="string", description="Remarks for the course")
     *                 )
     *             ),
     *             @OA\Property(property="guardian_info", type="array", description="Guardian details",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", description="Guardian's name"),
     *                     @OA\Property(property="phone", type="string", description="Guardian's phone number"),
     *                     @OA\Property(property="address", type="string", description="Guardian's address"),
     *                     @OA\Property(property="type", type="string", description="Type of guardian"),
     *                     @OA\Property(property="current_guardian", type="boolean", description="Is current guardian")
     *                 )
     *             ),
     *             @OA\Property(property="education_history", type="array", description="Educational background",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", description="Institution name"),
     *                     @OA\Property(property="address", type="string", description="Institution address"),
     *                     @OA\Property(property="marks_received", type="string", description="Marks received"),
     *                     @OA\Property(property="note", type="string", description="Additional notes"),
     *                     @OA\Property(property="course_studied", type="string", description="Course studied")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
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
     *                 @OA\Property(property="referral_source_id", type="integer", example=1),
     *                 @OA\Property(property="counselor_referred_id", type="array",
     *                     @OA\Items(type="integer", example=2)
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
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function singlepageStudentStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email',
                'address' => 'required|string',
                'phone' => 'required|string|max:15',
                'permanent_address' => 'nullable|string',
                'permanent_locality_id' => 'nullable|integer',
                'referral_source_id' => 'nullable|integer',
                'course_info' => 'nullable|array|min:1',
                'course_info.*.course_id' => 'nullable|exists:courses,id',
                'course_info.*.remarks' => 'nullable|string',
                'guardian_info' => 'nullable|array|min:1',
                'guardian_info.*.name' => 'nullable|string|max:255',
                'guardian_info.*.phone' => 'nullable|string|max:15',
                'guardian_info.*.address' => 'nullable|string',
                'guardian_info.*.type' => 'nullable|string',
                'guardian_info.*.current_guardian' => 'boolean',
                'education_history' => 'nullable|array|min:1',
                'education_history.*.name' => 'nullable|string|max:255',
                'education_history.*.address' => 'nullable|string',
                'education_history.*.marks_received' => 'nullable|string',
                'education_history.*.note' => 'nullable|string',
                'education_history.*.course_studied' => 'nullable|string',
                'counselor_referred_id' => 'nullable|array|min:1',
                'counselor_referred_id.*' => 'exists:counselor_referrers,id',
            ]);
            $student = Student::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'permanent_address' => $validated['permanent_address'] ?? null,
                'permanent_locality_id' => $validated['permanent_locality_id'] ?? null,
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
                        'counselor_phone' => $counselor->phone,
                        'counselor_role_name' => $counselor->role,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }else {
                    // If the counselor ID does not exist, store the provided data from the request
                    $counselorReferrers[] = [
                        'student_id' => $student->id,
                        'counselor_referred_id' => null, // No ID found in DB
                        'student_name' => $student->name,
                        'student_email' => $student->email,
                        'student_phone' => $student->phone,
                        'counselor_name' => $request->input('name', ''), // Default to 'Unknown' if not provided
                        'counselor_email' => $request->input('email', null),
                        'counselor_phone' => $request->input('phone', null),
                        'counselor_role_name' => $request->input('role', null),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            StudentCounselorReffer::insert($counselorReferrers);
            if (!empty($validated['course_info'])) {
                $courseEntries = array_map(fn($course) => [
                    'student_id' => $student->id,
                    'course_id' => $course['course_id'],
                    'remarks' => $course['remarks'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $validated['course_info']);
                StudentCourseInterest::insert($courseEntries);
            }
            if (!empty($validated['guardian_info'])) {
                $guardianEntries = array_map(fn($guardian) => [
                    'student_id' => $student->id,
                    'name' => $guardian['name'],
                    'phone' => $guardian['phone'],
                    'address' => $guardian['address'] ?? null,
                    'type' => $guardian['type'] ?? null,
                    'current_guardian' => $guardian['current_guardian'] ?? false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $validated['guardian_info']);
                StudentGuardianInfo::insert($guardianEntries);
            }
            if (!empty($validated['education_history'])) {
                $educationEntries = array_map(fn($edu) => [
                    'student_id' => $student->id,
                    'name' => $edu['name'],
                    'address' => $edu['address'] ?? null,
                    'marks_received' => $edu['marks_received'] ?? null,
                    'note' => $edu['note'] ?? null,
                    'course_studied' => $edu['course_studied'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $validated['education_history']);
                StudentEducationHistory::insert($educationEntries);
            }
            DB::commit();
            return response()->json([
                'message' => 'Student saved successfully',
                'data' => Student::with(['counselors', 'courseInterests', 'guardianInfo', 'educationHistory'])->find($student->id)
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
}
