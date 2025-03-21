<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentCounselorReffer;
use App\Models\CounselorReferrer;
use App\Models\StudentCourseInterest;
use App\Models\StudentDocument;
use App\Models\StudentEducationHistory;
use App\Models\StudentGuardianInfo;
use App\Models\StudentReferralSource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Exception;

class StudentSinglePageRestApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/single/page/students",
     *     summary="Get list of students",
     *     tags={"Student Single Page API"},
     *     security={{"Bearer": {}}},
     *    @OA\Parameter(
     *            name="per_page",
     *            in="query",
     *            description="Number of items per page (for pagination)",
     *            required=false,
     *            @OA\Schema(type="integer", default=10, example=10)
     *        ),
     *        @OA\Parameter(
     *            name="limit",
     *            in="query",
     *           description="Number of items to retrieve",
     *            required=false,
     *            @OA\Schema(type="integer", example=5)
     *        ),
     *        @OA\Parameter(
     *            name="offset",
     *            in="query",
     *            description="Number of items to skip (used with limit)",
     *            required=false,
     *            @OA\Schema(type="integer", example=0)
     *        ),
     *       @OA\Parameter(
     *             name="keyword",
     *             in="query",
     *             description="Search by name",
     *             required=false,
     *             @OA\Schema(type="string", example="shivam")
     *         ),
     *     @OA\Response(
     *          response=200,
     *          description="Student details retrieved successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Student details retrieved successfully"),
     *              @OA\Property(property="status", type="integer", example=1, description="Status code indicating success"),
     *              @OA\Property(property="data", type="object",
     *                   @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="name", type="string", example="John Doe"),
     *                   @OA\Property(property="email", type="string", example="john@gmail.com"),
     *                   @OA\Property(property="address", type="string", example="123 Main St"),
     *                   @OA\Property(property="phone", type="string", example="+1234567890"),
     *                   @OA\Property(property="permanent_locality_id", type="integer", example=1),
     *                   @OA\Property(property="counselor_referred_id", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="counselor_name", type="string", example="Jane Smith"),
     *                           @OA\Property(property="counselor_email", type="string", example="jane@gmail.com"),
     *                           @OA\Property(property="counselor_phone", type="string", example="+977 98525155"),
     *                          @OA\Property(property="counselor_role_name", type="string", example="Counselor"),
     *                       )
     *                   ),
     *                   @OA\Property(property="document_files", type="array", description="Uploaded document files",
     *                      @OA\Items(type="string", example="document1.pdf")
     *                   ),
     *                   @OA\Property(property="referral_sources", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="referral_source_name", type="string", example="Google"),
     *                           @OA\Property(property="referral_source_description", type="string", example="Google search engine")
     *                       )
     *                   ),
     *                   @OA\Property(property="course_interests", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="course_name", type="string", example="Computer Science"),
     *                           @OA\Property(property="remarks", type="string", example="Interested in the course")
     *                       )
     *                   ),
     *                   @OA\Property(property="follow up", type="array",
     *                        @OA\Items(
     *                            @OA\Property(property="date", type="string", example="date"),
     *                            @OA\Property(property="next date", type="string", example="next date"),
     *                            @OA\Property(property="status", type="string", example="visit")
     *                        )
     *                    ),
     *                   @OA\Property(property="permanent_locality_name", type="string", example="municiplity"),
     *                   @OA\Property(property="parent id", type="string", example="district"),
     *                   @OA\Property(property="adminstrative area", type="string", example="province"),
     *                   @OA\Property(property="country", type="string", example="nepal"),
     *                   @OA\Property(property="guardian_info", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="name", type="string", example="Jane Doe"),
     *                          @OA\Property(property="phone", type="string", example="9876543210"),
     *                           @OA\Property(property="address", type="string", example="789 Parent St, City"),
     *                         @OA\Property(property="type", type="string", example="Mother"),
     *                           @OA\Property(property="current_guardian", type="boolean", example=true)
     *                       )
     *                   ),
     *                   @OA\Property(property="education_history", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="name", type="string", example="XYZ High School"),
     *                           @OA\Property(property="address", type="string", example="123 School St, City"),
     *                           @OA\Property(property="marks_received", type="integer", example=85),
     *                           @OA\Property(property="note", type="string", example="Graduated with honors"),
     *                           @OA\Property(property="course_studied", type="string", example="Science")
     *                       )
     *                   )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No students found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No Student found"),
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="data", type="array", @OA\Items()),
     *             @OA\Property(property="pagination", type="string", nullable=true, example=null)
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10); // Default to 10 if not provided
            $limit = $request->input('limit');
            $offset = $request->input('offset', 0);
            $keyword = $request->input('keyword');

            // Start query builder with necessary relationships
            $query = Student::orderBy('created_at', 'desc')
                ->with([
                    'studentCounselorReffer',
                    'studentReferralSource.referralSource',
                    'courseInterests.course' => function ($query) {
                        $query->select('id', 'title');
                    },
                    'guardianInfo',
                    'educationHistory',
                    'studentDocuments',
                    'followUps.status',
                    'permanentLocality' => function ($query) {
                        $query->with('administrativeArea.parent.country');
                    },
                ]);

            // Add keyword search if applicable
            if (!empty($keyword)) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('email', 'LIKE', "%{$keyword}%")
                    ->orWhere('phone', 'LIKE', "%{$keyword}%")
                    ->orWhere('address', 'LIKE', "%{$keyword}%");
            }

            // Paginate with limit and offset or use perPage
            if ($limit && $offset >= 0) {
                $students = $query->offset($offset)->limit($limit)->get();
                $total = $query->count();
                $meta = [
                    'total' => $total,
                    'per_page' => (int)$limit,
                    'current_page' => (int)ceil(($offset + 1) / $limit),
                    'last_page' => (int)ceil($total / $limit),
                    'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                    'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
                ];
            } else {
                // Default pagination if no limit and offset are provided
                $students = $query->paginate($perPage);
                $meta = [
                    'total' => $students->total(),
                    'per_page' => $students->perPage(),
                    'current_page' => $students->currentPage(),
                    'last_page' => $students->lastPage(),
                    'next_page_url' => $students->nextPageUrl(),
                    'prev_page_url' => $students->previousPageUrl(),
                ];
            }

            // Process the documents if they exist
            foreach ($students as $student) {
                if (!empty($student->studentDocuments)) {
                    foreach ($student->studentDocuments as $document) {
                        if (!empty($document->document_file)) {
                            $document->document_file = url('/file/students/' . $document->document_file);
                        }
                    }
                }
            }

            // If no students are found, return 404
            if ($students->isEmpty()) {
                return response()->json([
                    'message' => 'No students found',
                    'status' => 0,
                    'data' => [],
                    'pagination' => null
                ], 404);
            }

            // Return success with paginated data
            return response()->json([
                'message' => 'Students retrieved successfully',
                'status' => 1,
                'data' => $students,
                'pagination' => $meta
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal Server Error',
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/single/page/students",
     *     summary="Create a new student",
     *     security={{"Bearer": {}}},
     *     tags={"Student Single Page API"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "address", "phone", "counselor_referred_id"},
     *             @OA\Property(property="name", type="string", description="Student's full name"),
     *             @OA\Property(property="email", type="string", format="email", description="Student's email address"),
     *             @OA\Property(property="address", type="string", description="Student's address"),
     *             @OA\Property(property="phone", type="string", description="Student's phone number"),
     *             @OA\Property(property="permanent_locality_id", type="integer", description="ID of the locality for the permanent address"),
     *             @OA\Property(property="referral_source_id", type="array", description="Array of referral source IDs",
     *                  @OA\Items(type="integer", description="Existing referral source ID")
     *              ),
     *              @OA\Property(property="document_file[]", type="array", description="Multiple document files",
     *                 @OA\Items(type="string", format="binary", description="File in PDF, DOC, DOCX, JPG, JPEG, or PNG format")
     *              ),
     *             @OA\Property(
     *                 property="counselor_referred_id",
     *                  type="array",
     *                 description="Array of counselor IDs or new counselor data",
     *                @OA\Items(
     *                     anyOf={
     *                         @OA\Schema(type="integer", description="Existing counselor ID"),
     *                        @OA\Schema(type="object", description="New counselor details",
     *                             @OA\Property(property="counselor_name", type="string", description="Name of the counselor"),
     *                             @OA\Property(property="counselor_email", type="string", description="Email of the counselor"),
     *                             @OA\Property(property="counselor_phone", type="string", description="Phone number of the counselor"),
     *                            @OA\Property(property="counselor_role_name", type="string", description="Role of the counselor")
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
     *
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
     *           response=200,
     *           description="Student details retrieved successfully",
     *           @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Student details retrieved successfully"),
     *               @OA\Property(property="status", type="integer", example=1, description="Status code indicating success"),
     *               @OA\Property(property="data", type="object",
     *                    @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                    @OA\Property(property="email", type="string", example="john@gmail.com"),
     *                    @OA\Property(property="address", type="string", example="123 Main St"),
     *                    @OA\Property(property="phone", type="string", example="+1234567890"),
     *                    @OA\Property(property="permanent_locality_id", type="integer", example=1),
     *                    @OA\Property(property="counselor_referred_id", type="array",
     *                        @OA\Items(
     *                            @OA\Property(property="counselor_name", type="string", example="Jane Smith"),
     *                            @OA\Property(property="counselor_email", type="string", example="jane@gmail.com"),
     *                            @OA\Property(property="counselor_phone", type="string", example="+977 98525155"),
     *                          @OA\Property(property="counselor_role_name", type="string", example="Counselor"),
     *                        )
     *                    ),
     *                    @OA\Property(property="document_files", type="array", description="Uploaded document files",
     *                       @OA\Items(type="string", example="document1.pdf")
     *                    ),
     *                   @OA\Property(property="referral_sources", type="array",
     *                        @OA\Items(
     *                            @OA\Property(property="referral_source_name", type="string", example="Google"),
     *                            @OA\Property(property="referral_source_description", type="string", example="Google search engine")
     *                        )
     *                    ),
     *                    @OA\Property(property="course_interests", type="array",
     *                        @OA\Items(
     *                            @OA\Property(property="course_name", type="string", example="Computer Science"),
     *                            @OA\Property(property="remarks", type="string", example="Interested in the course")
     *                        )
     *                    ),
     *                    @OA\Property(property="follow up", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="date", type="string", example="date"),
     *                             @OA\Property(property="next date", type="string", example="next date"),
     *                             @OA\Property(property="status", type="string", example="visit")
     *                        )
     *                     ),
     *                    @OA\Property(property="permanent_locality_name", type="string", example="municiplity"),
     *                    @OA\Property(property="parent id", type="string", example="district"),
     *                    @OA\Property(property="adminstrative area", type="string", example="province"),
     *                    @OA\Property(property="country", type="string", example="nepal"),
     *                   @OA\Property(property="guardian_info", type="array",
     *                        @OA\Items(
     *                           @OA\Property(property="name", type="string", example="Jane Doe"),
     *                           @OA\Property(property="phone", type="string", example="9876543210"),
     *                            @OA\Property(property="address", type="string", example="789 Parent St, City"),
     *                          @OA\Property(property="type", type="string", example="Mother"),
     *                            @OA\Property(property="current_guardian", type="boolean", example=true)
     *                        )
     *                    ),
     *                    @OA\Property(property="education_history", type="array",
     *                       @OA\Items(
     *                            @OA\Property(property="name", type="string", example="XYZ High School"),
     *                            @OA\Property(property="address", type="string", example="123 School St, City"),
     *                            @OA\Property(property="marks_received", type="integer", example=85),
     *                            @OA\Property(property="note", type="string", example="Graduated with honors"),
     *                            @OA\Property(property="course_studied", type="string", example="Science")
     *                        )
     *                    )
     *              )
     *          )
     *      ),
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
                'email' => 'nullable|email|unique:students,email',
                'address' => 'nullable|string',
                'phone' => 'nullable|string|max:15',
                'permanent_address' => 'nullable|string',
                'permanent_locality_id' => 'nullable|integer',
                'referral_source_id' => 'nullable|array|min:1',
                'referral_source_id.*' => 'exists:referral_sources,id',
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
                'counselor_referred_id.*' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if (is_numeric($value)) {
                            if (!\App\Models\CounselorReferrer::where('id', $value)->exists()) {
                                $fail("The selected $attribute is invalid.");
                            }
                        } elseif (is_array($value)) {
                            // Validate new counselor data if it's an array
                            $this->validateNewCounselorData($value, $fail, $attribute);
                        } else {
                            $fail("Invalid format for $attribute.");
                        }
                    }
                ],
                'document_file' => 'nullable|array',
                'document_file.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
            ]);
            $student = Student::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'permanent_address' => $validated['permanent_address'] ?? null,
                'permanent_locality_id' => $validated['permanent_locality_id'] ?? null,
            ]);

            $counselorReferrers = [];
            foreach ($validated['counselor_referred_id'] as $counselorReferrerId) {
                // Check if it's an existing counselor, otherwise create a new one
                $counselor = null;
                if (is_numeric($counselorReferrerId)) {
                    $counselor = CounselorReferrer::find($counselorReferrerId);
                }

                if ($counselor) {
                    // Existing counselor case
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
                } else {
                    // New counselor case
                    // Assume new counselor data comes from the request (e.g., name, email, role)
                    $newCounselor = new CounselorReferrer([
                       'name' => $counselorReferrerId['name'],  // Ensure 'name' is passed
                       'email' => $counselorReferrerId['email'], // Ensure 'email' is passed
                       'phone' => $counselorReferrerId['phone'], // Ensure 'phone' is passed
                       'role' => $counselorReferrerId['role'],   // Ensure 'role' is passed
                    ]);
                    $newCounselor->save();

                    $counselorReferrers[] = [
                        'student_id' => $student->id,
                        'counselor_referred_id' => $newCounselor->id,
                        'student_name' => $student->name,
                        'student_email' => $student->email,
                        'student_phone' => $student->phone,
                        'counselor_name' => $newCounselor->name,
                        'counselor_email' => $newCounselor->email,
                        'counselor_role_name' => $newCounselor->role,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insert counselor references
            StudentCounselorReffer::insert($counselorReferrers);
            //referral_source_id
            if (!empty($validated['referral_source_id'])) {
                $referralSources = array_map(fn($sourceId) => [
                    'student_id' => $student->id,
                    'referral_source_id' => $sourceId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $validated['referral_source_id']);
                StudentReferralSource::insert($referralSources);
            }

            //course_info, guardian_info, education_history
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
            // Document upload
            $fileDirectory = '/data/mfa/students/';
            if (!file_exists($fileDirectory)) {
                mkdir($fileDirectory, 0777, true);
            }
            if ($request->hasFile('document_file')) {
                $documentFileNames = [];
                foreach ($request->file('document_file') as $file) {
                    // Generate unique file name
                    $documentFileName = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    // Move the file to the desired location
                    $file->move($fileDirectory, $documentFileName);
                    // Store the file name for insertion
                    $documentFileNames[]  = $documentFileName;
                }
                foreach ($documentFileNames as $fileName) {
                    StudentDocument::create([
                        'student_id' => $student->id,
                        'document_file' => $fileName,
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Student saved successfully',
                'data' => Student::with([
                    'studentCounselorReffer',
                    'studentReferralSource',
                    'courseInterests',
                    'guardianInfo',
                    'educationHistory',
                    'studentDocuments'
                ])->find($student->id)
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

    private function validateNewCounselorData($value, $fail, $attribute)
{
    if (empty($value['name']) || empty($value['email']) || empty($value['phone']) || empty($value['role'])) {
        $fail("For new counselors, none of the fields (name, email, phone, role) can be empty.");
    }
}
    /**
     * @OA\Put(
     *     path="/api/v1/single/page/students/{id}",
     *     summary="Update student details",
     *     description="Updates the details of a student along with counselor references, referral sources, course info, guardian info, and education history.",
     *     operationId="updateStudent",
     *     tags={"Student Single Page API"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the student to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                     type="object",
     *                     required={"name", "email", "phone", "counselor_referred_id"},
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                     @OA\Property(property="address", type="string", example="123 Main St, City"),
     *                     @OA\Property(property="phone", type="string", example="123-456-7890"),
     *                     @OA\Property(property="permanent_locality_id", type="integer", example=1, nullable=true),
     *             @OA\Property(
     *                  property="counselor_referred_id",
     *                   type="array",
     *                  description="Array of counselor IDs or new counselor data",
     *                 @OA\Items(
     *                      anyOf={
     *                        @OA\Schema(type="integer", description="Existing counselor ID"),
     *                         @OA\Schema(type="object", description="New counselor details",
     *                              @OA\Property(property="counselor_name", type="string", description="Name of the counselor"),
     *                              @OA\Property(property="counselor_email", type="string", description="Email of the counselor"),
     *                              @OA\Property(property="counselor_phone", type="string", description="Phone number of the counselor"),
     *                             @OA\Property(property="counselor_role_name", type="string", description="Role of the counselor")
     *                         )
     *                     }
     *                  )
     *              ),
     *                     @OA\Property(property="referral_source_id", type="array", @OA\Items(type="integer", example=2), nullable=true),
     *                     @OA\Property(property="course_info", type="array", @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="course_id", type="integer", example=1),
     *                         @OA\Property(property="remarks", type="string", example="Interested in the course")
     *                     ), nullable=true),
     *                     @OA\Property(property="guardian_info", type="array", @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="name", type="string", example="Jane Doe"),
     *                         @OA\Property(property="phone", type="string", example="987-654-3210"),
     *                         @OA\Property(property="address", type="string", example="789 Parent St, City", nullable=true),
     *                         @OA\Property(property="type", type="string", example="Mother", nullable=true),
     *                         @OA\Property(property="current_guardian", type="boolean", example=true)
     *                     ), nullable=true),
     *                     @OA\Property(property="education_history", type="array", @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="name", type="string", example="XYZ High School"),
     *                         @OA\Property(property="address", type="string", example="123 School St, City", nullable=true),
     *                         @OA\Property(property="marks_received", type="integer", example=85, nullable=true),
     *                         @OA\Property(property="note", type="string", example="Graduated with honors", nullable=true),
     *                         @OA\Property(property="course_studied", type="string", example="Science", nullable=true)
     *                     ), nullable=true),
     *                         @OA\Property(property="document_files", type="array", description="Uploaded document files",
     *                        @OA\Items(type="string", example="document1.pdf")
     *                     )
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Student details retrieved successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Student details retrieved successfully"),
     *              @OA\Property(property="status", type="integer", example=1, description="Status code indicating success"),
     *              @OA\Property(property="data", type="object",
     *                   @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                   @OA\Property(property="email", type="string", example="john@gmail.com"),
     *                   @OA\Property(property="address", type="string", example="123 Main St"),
     *                   @OA\Property(property="phone", type="string", example="+1234567890"),
     *                   @OA\Property(property="permanent_locality_id", type="integer", example=1),
     *                   @OA\Property(property="counselors", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="counselor_name", type="string", example="Jane Smith"),
     *                             @OA\Property(property="counselor_email", type="string", example="jane@gmail.com"),
     *                             @OA\Property(property="counselor_phone", type="string", example="+977 98525155"),
     *                            @OA\Property(property="counselor_role_name", type="string", example="Counselor"),
     *
     *                       )
     *                   ),
     *                  @OA\Property(property="referral_sources", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="referral_source_name", type="string", example="Google"),
     *                           @OA\Property(property="referral_source_description", type="string", example="Google search engine")
     *                       )
     *                   ),
     *                @OA\Property(property="document_files", type="array", description="Uploaded document files",
     *                     @OA\Items(type="string", example="document1.pdf")
     *                    ),
     *                  @OA\Property(property="course_interests", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="course_name", type="string", example="Computer Science"),
     *                           @OA\Property(property="remarks", type="string", example="Interested in the course")
     *                       )
     *                   ),
     *                   @OA\Property(property="follow up", type="array",
     *                        @OA\Items(
     *                            @OA\Property(property="date", type="string", example="date"),
     *                            @OA\Property(property="next date", type="string", example="next date"),
     *                            @OA\Property(property="status", type="string", example="visit")
     *                        )
     *                    ),
     *                     @OA\Property(property="permanent_locality_name", type="string", example="municiplity"),
     *                     @OA\Property(property="parent id", type="string", example="district"),
     *                     @OA\Property(property="adminstrative area", type="string", example="province"),
     *                      @OA\Property(property="country", type="string", example="nepal"),
     *                   @OA\Property(property="guardian_info", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="name", type="string", example="Jane Doe"),
     *                           @OA\Property(property="phone", type="string", example="9876543210"),
     *                          @OA\Property(property="address", type="string", example="789 Parent St, City"),
     *                           @OA\Property(property="type", type="string", example="Mother"),
     *                           @OA\Property(property="current_guardian", type="boolean", example=true)
     *                       )
     *                   ),
     *                   @OA\Property(property="education_history", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="name", type="string", example="XYZ High School"),
     *                           @OA\Property(property="address", type="string", example="123 School St, City"),
     *                           @OA\Property(property="marks_received", type="integer", example=85),
     *                           @OA\Property(property="note", type="string", example="Graduated with honors"),
     *                           @OA\Property(property="course_studied", type="string", example="Science")
     *                       )
     *                   )
     *              )
     *      )
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
     *         response=404,
     *         description="Student not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student not found")
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
    public function update(Request $request, $id)
    {
        try {
            $student = Student::find($id);
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email,' . $id,
                'address' => 'required|string',
                'phone' => 'required|string|max:15',
                'permanent_address' => 'nullable|string',
                'permanent_locality_id' => 'nullable|integer',
                'referral_source_id' => 'nullable|array|min:1',
                'referral_source_id.*' => 'exists:referral_sources,id',
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
                'document_file' => 'nullable|array',
                'document_file.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
            ]);
            // Update student details
            $student->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'permanent_address' => $validated['permanent_address'] ?? null,
                'permanent_locality_id' => $validated['permanent_locality_id'] ?? null,
            ]);
            // Insert updated counselor references
            $counselorReferrers = [];
            foreach ($validated['counselor_referred_id'] as $counselorReferrerId) {
                // Check if the counselor reference already exists for the student
                $existingCounselor = StudentCounselorReffer::where('student_id', $student->id)
                    ->where('counselor_referred_id', $counselorReferrerId)
                    ->first();

                if (!$existingCounselor) {
                    // Try to find the counselor by ID
                    $counselor = CounselorReferrer::find($counselorReferrerId);

                    if ($counselor) {
                        // If counselor exists, use data from the database
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
                    } else {
                        // If counselor ID does not exist in DB, use the provided request data
                        $counselorReferrers[] = [
                            'student_id' => $student->id,
                            'counselor_referred_id' => null, // No ID found in DB
                            'student_name' => $student->name,
                            'student_email' => $student->email,
                            'student_phone' => $student->phone,
                            'counselor_name' => $request->input('name', 'Unknown'), // Default to 'Unknown' if not provided
                            'counselor_email' => $request->input('email', null),
                            'counselor_phone' => $request->input('phone', null),
                            'counselor_role_name' => $request->input('role', null),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
            StudentCounselorReffer::insert($counselorReferrers);
            // Referral source updates (if any)
            if (!empty($validated['referral_source_id'])) {
                $existingReferralSources = StudentReferralSource::where('student_id', $student->id)->pluck('referral_source_id')->toArray();
                $newReferralSources = array_diff($validated['referral_source_id'], $existingReferralSources);
                $referralSources = array_map(fn($sourceId) => [
                    'student_id' => $student->id,
                    'referral_source_id' => $sourceId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $newReferralSources);
                StudentReferralSource::insert($referralSources);
            }
            // Course info updates (if any)
            if (!empty($validated['course_info'])) {
                foreach ($validated['course_info'] as $course) {
                    // Check if course entry already exists
                    $existingCourse = StudentCourseInterest::where('student_id', $student->id)
                        ->where('course_id', $course['course_id'])
                        ->first();
                    if (!$existingCourse) {
                        // Insert if no existing record
                        StudentCourseInterest::create([
                            'student_id' => $student->id,
                            'course_id' => $course['course_id'],
                            'remarks' => $course['remarks'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            // Guardian info updates (if any)
            if (!empty($validated['guardian_info'])) {
                foreach ($validated['guardian_info'] as $guardian) {
                    // Check if guardian entry already exists
                    $existingGuardian = StudentGuardianInfo::where('student_id', $student->id)
                        ->where('name', $guardian['name'])
                        ->first();
                    if (!$existingGuardian) {
                        // Insert if no existing record
                        StudentGuardianInfo::create([
                            'student_id' => $student->id,
                            'name' => $guardian['name'],
                            'phone' => $guardian['phone'],
                            'address' => $guardian['address'] ?? null,
                            'type' => $guardian['type'] ?? null,
                            'current_guardian' => $guardian['current_guardian'] ?? false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            // Education history updates (if any)
            if (!empty($validated['education_history'])) {
                foreach ($validated['education_history'] as $edu) {
                    // Check if education history entry already exists
                    $existingEducation = StudentEducationHistory::where('student_id', $student->id)
                        ->where('name', $edu['name'])
                        ->first();
                    if (!$existingEducation) {
                        // Insert if no existing record
                        StudentEducationHistory::create([
                            'student_id' => $student->id,
                            'name' => $edu['name'],
                            'address' => $edu['address'] ?? null,
                            'marks_received' => $edu['marks_received'] ?? null,
                            'note' => $edu['note'] ?? null,
                            'course_studied' => $edu['course_studied'] ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            // Document upload
            $fileDirectory = '/data/mfa/students/';
            if (!file_exists($fileDirectory)) {
                mkdir($fileDirectory, 0777, true);
            }
            if ($request->hasFile('document_file')) {
                // Delete old files (optional)
                $existingFiles = StudentDocument::where('student_id', $student->id)->get();
                foreach ($existingFiles as $existingFile) {
                    $oldFilePath = $fileDirectory . $existingFile->document_file;
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath); // Delete the old file
                    }
                }
                // Remove old database records
                StudentDocument::where('student_id', $student->id)->delete();
                // Upload new files
                $documentFileNames = [];
                foreach ($request->file('document_file') as $file) {
                    // Generate unique file name
                    $documentFileName = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    // Move the file to the desired location
                    $file->move($fileDirectory, $documentFileName);
                    // Store the file name for database insertion
                    $documentFileNames[] = $documentFileName;
                }
                // Insert new records in the database
                foreach ($documentFileNames as $fileName) {
                    StudentDocument::create([
                        'student_id' => $student->id,
                        'document_file' => $fileName,
                    ]);
                }
            }
            return response()->json([
                'message' => 'Student updated successfully',
                'data' => $student->load(
                    'studentCounselorReffer',
                    'studentReferralSource',
                    'courseInterests',
                    'guardianInfo',
                    'educationHistory',
                    'studentDocuments'
                )
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
     * @OA\Get(
     *     path="/api/v1/single/page/students/{id}",
     *     summary="Get student details",
     *     tags={"Student Single Page API"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the student to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student details retrieved successfully"),
     *             @OA\Property(property="status", type="integer", example=1, description="Status code indicating success"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", example="john@gmail.com"),
     *                  @OA\Property(property="address", type="string", example="123 Main St"),
     *                  @OA\Property(property="phone", type="string", example="+1234567890"),                  @OA\Property(property="permanent_locality_id", type="integer", example=1),
     *                  @OA\Property(property="counselors", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="counselor_name", type="string", example="Jane Smith"),
     *                            @OA\Property(property="counselor_email", type="string", example="jane@gmail.com"),
     *                            @OA\Property(property="counselor_phone", type="string", example="+977 98525155"),
     *                           @OA\Property(property="counselor_role_name", type="string", example="Counselor"),
     *                      )
     *                  ),
     *                  @OA\Property(property="referral_sources", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="referral_source_name", type="string", example="Google"),
     *                          @OA\Property(property="referral_source_description", type="string", example="Google search engine")
     *                      )
     *                  ),
     *               @OA\Property(property="document_files", type="array", description="Uploaded document files",
     *                    @OA\Items(type="string", example="document1.pdf")
     *                   ),
     *                 @OA\Property(property="course_interests", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="course_name", type="string", example="Computer Science"),
     *                          @OA\Property(property="remarks", type="string", example="Interested in the course")
     *                      )
     *                  ),
     *                  @OA\Property(property="follow up", type="array",
     *                       @OA\Items(
     *                           @OA\Property(property="date", type="string", example="date"),
     *                           @OA\Property(property="next date", type="string", example="next date"),
     *                           @OA\Property(property="status", type="string", example="visit")
     *                       )
     *                   ),
     *                    @OA\Property(property="permanent_locality_name", type="string", example="municiplity"),
     *                    @OA\Property(property="parent id", type="string", example="district"),
     *                    @OA\Property(property="adminstrative area", type="string", example="province"),
     *                     @OA\Property(property="country", type="string", example="nepal"),
     *                  @OA\Property(property="guardian_info", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="name", type="string", example="Jane Doe"),
     *                          @OA\Property(property="phone", type="string", example="9876543210"),
     *                          @OA\Property(property="address", type="string", example="789 Parent St, City"),
     *                          @OA\Property(property="type", type="string", example="Mother"),
     *                          @OA\Property(property="current_guardian", type="boolean", example=true)
     *                      )
     *                  ),
     *                  @OA\Property(property="education_history", type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="name", type="string", example="XYZ High School"),
     *                          @OA\Property(property="address", type="string", example="123 School St, City"),
     *                          @OA\Property(property="marks_received", type="integer", example=85),
     *                          @OA\Property(property="note", type="string", example="Graduated with honors"),
     *                          @OA\Property(property="course_studied", type="string", example="Science")
     *                      )
     *                  )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $student = Student::with([
            'studentCounselorReffer',
            'studentReferralSource.referralSource',
            'courseInterests.course' => function ($query) {
                $query->select('id', 'title');
            },
            'guardianInfo',
            'educationHistory',
            'studentDocuments',
            'followUps.status',
            'permanentLocality' => function ($query) {
                $query->with('administrativeArea.parent.country');
            },
        ])->find($id);
        if (!empty($student->studentDocuments)) {
            foreach ($student->studentDocuments as $document) {
                if (!empty($document->document_file)) {
                    $document->document_file = url('/file/students/' . $document->document_file);
                }
            }
        }
        if (!$student) {
            return response()->json([
                'message' => 'Student not found',
                'status' => 0,
                'data' => []
            ], 404);
        }
        return response()->json([
            'message' => 'Student details retrieved successfully',
            'status' => 1,
            'data' => $student
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/single/page/students/{id}",
     *     summary="Delete a student",
     *     tags={"Student Single Page API"},
     *     security={{"Bearer": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the student to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student deleted successfully"),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Student not found"),
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json([
                'message' => 'Student not found',
                'status' => 0,
                'data' => []
            ], 404);
        }
        $student->delete();
        return response()->json([
            'message' => 'Student deleted successfully',
            'status' => 1,
            'data' => []
        ], 200);
    }
}
