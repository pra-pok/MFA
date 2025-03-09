<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;

class SearchRestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/search",
     *     summary="Search for courses, universities, and organizations",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Search keyword",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Search type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items to get",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Number of items to skip",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="courses", type="object",
     *                     @OA\Property(property="items", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="course_title", type="string", example="Course Name"),
     *                             @OA\Property(property="short_title", type="string", example="Short Name"),
     *                             @OA\Property(property="slug", type="string", example="course-slug"),
     *                             @OA\Property(property="duration", type="string", example="3 years")
     *                         )
     *                     ),
     *                     @OA\Property(property="meta", type="object")
     *                 ),
     *                 @OA\Property(property="universities", type="object",
     *                     @OA\Property(property="items", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="university_title", type="string", example="University Name"),
     *                             @OA\Property(property="slug", type="string", example="university-slug")
     *                         )
     *                     ),
     *                     @OA\Property(property="meta", type="object")
     *                 ),
     *                 @OA\Property(property="organizations", type="object",
     *                     @OA\Property(property="items", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="organization_name", type="string", example="Organization Name"),
     *                             @OA\Property(property="short_name", type="string", example="Org Short Name"),
     *                             @OA\Property(property="slug", type="string", example="organization-slug"),
     *                             @OA\Property(property="logo", type="string", example="https://example.com/logo.png"),
     *                             @OA\Property(property="banner_image", type="string", example="https://example.com/banner.png"),
     *                             @OA\Property(property="address", type="string", example="City, Country"),
     *                             @OA\Property(property="review_count", type="integer", example=10),
     *                             @OA\Property(property="average_rating", type="number", format="float", example=4.5)
     *                         )
     *                     ),
     *                     @OA\Property(property="meta", type="object")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function getSearch(Request $request)
    {
        $search = $request->query('keyword', '');
        $type = $request->query('type', 'advanced');
        $perPage = (int) $request->query('per_page', 10);
        $limit = $request->query('limit');
        $offset = (int) $request->query('offset', 0);
        $courseQuery = DB::table('courses')
            ->select('id', 'title as course_title', 'short_title', 'slug', 'duration')
            ->where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('short_title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        $universityQuery = DB::table('universities')
            ->select('id', 'title as university_title', 'slug','logo')
            ->where('status', 1)
            ->where('title', 'like', "%$search%");
        $organizationQuery = DB::table('organizations')
            ->select('id', 'name as organization_name', 'short_name', 'slug', 'logo', 'address', 'banner_image')
            ->where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('short_name', 'like', "%$search%");
            });
        if ($limit) {
            $totalCourses = $courseQuery->count();
            $totalUniversities = $universityQuery->count();
            $totalOrganizations = $organizationQuery->count();
            $courses = $courseQuery->limit($limit)->offset($offset)->get();
            $universities = $universityQuery->limit($limit)->offset($offset)->get();
            $organizations = $organizationQuery->limit($limit)->offset($offset)->get();
            $metaCourses = $this->generateMeta($totalCourses, $limit, $offset, $search);
            $metaUniversities = $this->generateMeta($totalUniversities, $limit, $offset, $search);
            $metaOrganizations = $this->generateMeta($totalOrganizations, $limit, $offset, $search);
        } else {
            $courses = $courseQuery->paginate($perPage);
            $universities = $universityQuery->paginate($perPage);
            $organizations = $organizationQuery->paginate($perPage);
            $metaCourses = $this->extractPaginationMeta($courses);
            $metaUniversities = $this->extractPaginationMeta($universities);
            $metaOrganizations = $this->extractPaginationMeta($organizations);
            $courses = $courses->items();
            $universities = $universities->items();
            $organizations = $organizations->items();
        }
        $organizations = collect($organizations)->map(function ($organization) {
            $organization->logo = !empty($organization->logo) ? url('/file/organization/' . $organization->logo) : '';
            $organization->banner_image = !empty($organization->banner_image) ? url('/file/organization_banner/' . $organization->banner_image) : '';
            $organization->review_count = Review::where('organization_id', $organization->id)->count();
            $organization->average_rating = Review::where('organization_id', $organization->id)->avg('rating');

            return $organization;
        });
        $universities = collect($universities)->map(function ($university) {
            $university->logo = !empty($university->logo) ? url('/file/university/' . $university->logo) : '';
            return $university;
        });
        return response()->json([
            "data" => [
                "courses" => [
                    "items" => $courses,
                    "meta" => $metaCourses
                ],
                "universities" => [
                    "items" => $universities,
                    "meta" => $metaUniversities
                ],
                "organizations" => [
                    "items" => $organizations,
                    "meta" => $metaOrganizations
                ]
            ],
            "message" => "",
            "status" => true,
            "timestamp" => now()->toISOString(),
        ]);
    }
    private function generateMeta($total, $limit, $offset, $search)
    {
        return [
            'total' => $total,
            'per_page' => (int) $limit,
            'current_page' => (int) ceil(($offset + 1) / $limit),
            'last_page' => (int) ceil($total / $limit),
            'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?keyword=$search&limit=$limit&offset=" . ($offset + $limit) : null,
            'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?keyword=$search&limit=$limit&offset=" . ($offset - $limit) : null,
        ];
    }
    private function extractPaginationMeta($pagination)
    {
        return [
            'total' => $pagination->total(),
            'per_page' => $pagination->perPage(),
            'current_page' => $pagination->currentPage(),
            'last_page' => $pagination->lastPage(),
            'next_page_url' => $pagination->nextPageUrl(),
            'prev_page_url' => $pagination->previousPageUrl(),
        ];
    }

//    public function getSimpleSearch(Request $request)
//    {
//        $search = $request->query('keyword', '');
//        $type = $request->query('type', 'simple');
//        $perPage = (int) $request->query('per_page', 10);
//        $page = (int) $request->query('page', 1);
//        $offset = ($page - 1) * $perPage;
//        $query = DB::table('courses')
//            ->select('id', 'title', DB::raw("'course' as type"))
//            ->where('title', 'like', "%$search%")
//            ->orWhere('short_title', 'like', "%$search%")
//            ->union(
//                DB::table('universities')
//                    ->select('id', 'title', DB::raw("'university' as type"))
//                    ->where('title', 'like', "%$search%")
//                    ->orWhere('short_title', 'like', "%$search%")
//
//            )
//            ->union(
//                DB::table('organizations')
//                    ->select('id', 'name as title', DB::raw("'organization' as type"))
//                    ->where('name', 'like', "%$search%")
//                    ->orWhere('short_name', 'like', "%$search%")
//                    ->orWhere('search_keywords', 'like', "%$search%")
//            );
//        $total = DB::table(DB::raw("({$query->toSql()}) as sub"))
//            ->mergeBindings($query)
//            ->count();
//        $results = DB::table(DB::raw("({$query->toSql()}) as sub"))
//            ->mergeBindings($query)
//            ->offset($offset)
//            ->limit($perPage)
//            ->get();
//        $response = [
//            "data" => $results,
//            "meta" => [
//                "total" => $total,
//                "per_page" => $perPage,
//                "current_page" => $page,
//                "last_page" => ceil($total / $perPage),
//                "next_page_url" => $page < ceil($total / $perPage) ? url()->current() . "?page=" . ($page + 1) . "&per_page=" . $perPage : null,
//                "prev_page_url" => $page > 1 ? url()->current() . "?page=" . ($page - 1) . "&per_page=" . $perPage : null,
//            ],
//            "message" => "",
//            "status" => true,
//            "timestamp" => now()->toIso8601String()
//        ];
//
//        return response()->json($response);
//    }

    /**
     * @OA\Get(
     *     path="/api/v1/search/college/course/university",
     *     summary="Search for Colleges, Courses, and Universities",
     *     description="This endpoint allows you to perform a simple search across colleges, courses, and universities. You can filter results by keyword, course ID, and university ID. The results are paginated for better performance.",
     *     tags={"Search"},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Keyword to search for across college, course, and university names. It searches both title and short title.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Specify the search type. This field defaults to 'simple' if not provided.",
     *         required=false,
     *         @OA\Schema(type="string", default="simple")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page for paginated results. Defaults to 10.",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Current page number for pagination. Defaults to 1.",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="course",
     *         in="query",
     *         description="Filter by specific course ID to narrow down search results to a particular course.",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="university",
     *         in="query",
     *         description="Filter by specific university ID to narrow down search results to a particular university.",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Course Title"),
     *                 @OA\Property(property="short_title", type="string", example="Short Title"),
     *                 @OA\Property(property="duration", type="string", example="2 Years"),
     *                 @OA\Property(property="min_range_fee", type="number", example=1000),
     *                 @OA\Property(property="max_range_fee", type="number", example=5000),
     *                 @OA\Property(property="type", type="string", example="course"),
     *                 @OA\Property(property="address", type="string", example="123 University St"),
     *                 @OA\Property(property="logo", type="string", format="url", example="https://example.com/logo.png"),
     *                 @OA\Property(property="banner_image", type="string", format="url", example="https://example.com/banner.png"),
     *                 @OA\Property(property="review_count", type="integer", example=10),
     *                 @OA\Property(property="average_rating", type="number", format="float", example=4.5)
     *             )),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="https://example.com/api/v1/search-college-course-university?page=2&per_page=10"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-03-03T12:00:00Z")
     *         )
     *     )
     * )
     */
    public function getSimpleSearch(Request $request)
    {
        $search = $request->query('keyword', '');
        $type = $request->query('type', 'simple');
        $perPage = (int) $request->query('per_page', 10);
        $page = (int) $request->query('page', 1);
        $offset = ($page - 1) * $perPage;
        $courseId = $request->query('course');
        $universityId = $request->query('university');
        $courses = DB::table('courses')
            ->select('id', 'title','short_title','duration','min_range_fee','max_range_fee', DB::raw("'course' as type"))
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('short_title', 'like', "%$search%");
            })
            ->when($courseId, fn($query) => $query->where('id', $courseId))
            ->get();
        $universities = DB::table('universities')
            ->select('id', 'title', DB::raw("'university' as type"))
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('short_title', 'like', "%$search%");
            })
            ->when($universityId, fn($query) => $query->where('id', $universityId))
            ->get();
        $organizations = DB::table('organizations')
            ->select(
                'organizations.id',
                'organizations.name as title',
                'organizations.address',
                'organizations.logo',
                'organizations.banner_image',
                DB::raw("'organization' as type"),
                DB::raw('(SELECT COUNT(*) FROM reviews WHERE reviews.organization_id = organizations.id) AS review_count'),
                DB::raw('(SELECT AVG(reviews.rating) FROM reviews WHERE reviews.organization_id = organizations.id) AS average_rating')
            )
            ->leftJoin('organization_courses', 'organizations.id', '=', 'organization_courses.organization_id')
            ->when($search, function ($query) use ($search) {
                $query->where('organizations.name', 'like', "%$search%")
                    ->orWhere('organizations.short_name', 'like', "%$search%")
                    ->orWhere('organizations.search_keywords', 'like', "%$search%");
            })
            ->when($courseId, fn($query) => $query->where('organization_courses.course_id', $courseId))
            ->when($universityId, fn($query) => $query->where('organization_courses.university_id', $universityId))
            ->distinct()
            ->get()
            ->map(function ($org) {
                return [
                    'id' => $org->id,
                    'title' => $org->title,
                    'address' => $org->address,
                    'logo' => !empty($org->logo) ? url('/file/organization/' . $org->logo) : null,
                    'banner_image' => !empty($org->banner_image) ? url('/file/organization_banner/' . $org->banner_image) : null,
                    'type' => $org->type,
                    'review_count' => $org->review_count,
                    'average_rating' => $org->average_rating ? round($org->average_rating, 2) : 0,
                ];
            });
        $results = $courses->merge($universities)->merge($organizations);
        $total = $results->count();
        $paginatedResults = $results->slice($offset, $perPage)->values();
        $response = [
            "data" => $paginatedResults,
            "meta" => [
                "total" => $total,
                "per_page" => $perPage,
                "current_page" => $page,
                "last_page" => ceil($total / $perPage),
                "next_page_url" => $page < ceil($total / $perPage) ? url()->current() . "?page=" . ($page + 1) . "&per_page=" . $perPage : null,
                "prev_page_url" => $page > 1 ? url()->current() . "?page=" . ($page - 1) . "&per_page=" . $perPage : null,
            ],
            "message" => "",
            "status" => true,
            "timestamp" => now()->toIso8601String()
        ];

        return response()->json($response);
    }
}
