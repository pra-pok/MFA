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
            ->select('id', 'title as university_title', 'slug')
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
            ->select('id', 'title', DB::raw("'course' as type"))
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
            ->select('organizations.id', 'organizations.name as title', DB::raw("'organization' as type"))
            ->leftJoin('organization_courses', 'organizations.id', '=', 'organization_courses.organization_id')
            ->when($search, function ($query) use ($search) {
                $query->where('organizations.name', 'like', "%$search%")
                    ->orWhere('organizations.short_name', 'like', "%$search%")
                    ->orWhere('organizations.search_keywords', 'like', "%$search%");
            })
            ->when($courseId, fn($query) => $query->where('organization_courses.course_id', $courseId))
            ->when($universityId, fn($query) => $query->where('organization_courses.university_id', $universityId))
            ->distinct()
            ->get();
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
