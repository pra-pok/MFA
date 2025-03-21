<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;

class ConfigSearchRestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/config/search",
     *     summary="Get search configuration",
     *     tags={"Config Search"},
     *     description="Retrieve available courses, universities, types of colleges, durations, and organizations based on filters.",
     *     @OA\Parameter(
     *         name="course",
     *         in="query",
     *         description="Filter by Course ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="university",
     *         in="query",
     *         description="Filter by University ID",
     *         required=false,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Parameter(
     *         name="duration",
     *         in="query",
     *         description="Filter by course duration (in years)",
     *         required=false,
     *         @OA\Schema(type="integer", example=4)
     *     ),
     *     @OA\Parameter(
     *         name="type-of-college",
     *         in="query",
     *         description="Filter by type of college",
     *         required=false,
     *         @OA\Schema(type="string", example="Public")
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Search by organization name",
     *         required=false,
     *         @OA\Schema(type="string", example="Engineering")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10, example=10)
     *     ),
     *     @OA\Parameter(
     *            name="limit",
     *            in="query",
     *            description="Number of items to retrieve",
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
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-03-03T12:00:00Z"),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true, example="https://example.com/api/v1/config/search?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="course", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Computer Science")
     *                 )),
     *                 @OA\Property(property="university", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="title", type="string", example="Harvard University")
     *                 )),
     *                 @OA\Property(property="type-of-college", type="array", @OA\Items(
     *                     @OA\Property(property="key", type="string", example="Public"),
     *                     @OA\Property(property="value", type="string", example="Public")
     *                 )),
     *                 @OA\Property(property="duration", type="array", @OA\Items(
     *                     @OA\Property(property="key", type="string", example="4"),
     *                     @OA\Property(property="value", type="string", example="4 Years")
     *                 )),
     *                 @OA\Property(property="organizations", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Tech Academy"),
     *                     @OA\Property(property="type", type="string", example="Private"),
     *                     @OA\Property(property="address", type="string", example="123 Main St"),
     *                     @OA\Property(property="logo", type="string", format="url", example="https://example.com/logo.png"),
     *                     @OA\Property(property="banner_image", type="string", format="url", example="https://example.com/banner.png"),
     *                     @OA\Property(property="review_count", type="integer", example=15),
     *                     @OA\Property(property="average_rating", type="number", format="float", example=4.3)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="No results found")
     * )
     */
    public function getConfigSearch(Request $request)
    {
        $data['course'] = DB::table('courses')
            ->select('id', 'title', 'short_title', DB::raw("'course' as type"))
            ->get();
        $data['university'] = DB::table('universities')
            ->select('id', 'title', DB::raw("'university' as type"))
            ->get();
        $data['type-of-college'] = ['Public' => 'Public', 'Private' => 'Private', 'Community (Aided)' => 'Community (Aided)'
            , 'Community (Managed)' => 'Community (Managed)', 'Community (Teacher Aid)' => 'Community (Teacher Aid)', 'Community (Unaided)' => 'Community (Unaided)'
            , 'Institutional (Private)' => 'Institutional (Private)', 'Institutional (Public)' => 'Institutional (Public)', 'Institutional (Company)' => 'Institutional (Company)'
            , 'Public with religious' => 'Public with religious', 'Madrasa' => 'Madrasa', 'Gumba' => 'Gumba', 'Ashram' => 'Ashram',
            'SOP/FSP' => 'SOP/FSP', 'Community ECD' => 'Community ECD', 'Other' => 'Other'];
        $data['duration'] = ['1' => '1 Year', '2' => '2 Year', '3' => '3 Year', '4' => '4 Year', '5' => '5 Year'];
        $query = Organization::query()
            ->leftJoin('organization_courses', 'organizations.id', '=', 'organization_courses.organization_id')
            ->leftJoin('courses', 'organization_courses.course_id', '=', 'courses.id')
            ->leftJoin('universities', 'organization_courses.university_id', '=', 'universities.id')
            ->select(
                'organizations.id',
                'organizations.name',
                'organizations.type',
                'organizations.address',
                'organizations.logo',
                'organizations.banner_image',
                 DB::raw('(SELECT COUNT(*) FROM reviews WHERE reviews.organization_id = organizations.id) AS review_count'),
                 DB::raw('(SELECT AVG(reviews.rating) FROM reviews WHERE reviews.organization_id = organizations.id) AS average_rating')
            )
            ->distinct();
        if ($request->filled('course')) {
            $query->where('organization_courses.course_id', $request->course);
        }
        if ($request->filled('university')) {
            $query->where('organization_courses.university_id', $request->university);
        }
        if ($request->filled('duration')) {
            $query->where('courses.duration', (int) $request->duration);
        }
        if ($request->filled('type-of-college')) {
            $query->whereNotNull('organizations.type')
                ->where('organizations.type', $request->input('type-of-college'));
        }
        if ($request->filled('keyword')) {
            $query->where('organizations.name', 'LIKE', '%' . $request->keyword . '%');
        }
        $query->orderByDesc('review_count')->orderByDesc('average_rating');
        // Adding limit and offset for pagination


        $data['organizations'] = $query->get()->map(function ($org) {
            return [
                'id' => $org->id,
                'name' => $org->name,
                'type' => $org->type,
                'address' => $org->address,
                'logo' => !empty($org->logo) ? url('/file/organization/' . $org->logo) : null,
                'banner_image' => !empty($org->banner_image) ? url('/file/organization_banner/' . $org->banner_image) : null,
                'review_count' => $org->review_count,
                'average_rating' => $org->average_rating ? round($org->average_rating, 2) : 0,
            ];
        });
        $perPage = $request->input('per_page', 10); // Default per page 10
        $limit = $request->input('limit', 10); // Default limit
        $offset = $request->input('offset', 0); // Default offset

        if ($limit) {
            $total = $query->count();
            $organizations = $query->limit($limit)->offset($offset)->get();
            $meta = [
                'total' => $total,
                'per_page' => (int) $limit,
                'current_page' => (int) ceil(($offset + 1) / $limit),
                'last_page' => (int) ceil($total / $limit),
                'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
            ];
        } else {
            $paginatedOrganizations = $query->paginate($perPage);
            $meta = [
                'total' => $paginatedOrganizations->total(),
                'per_page' => $paginatedOrganizations->perPage(),
                'current_page' => $paginatedOrganizations->currentPage(),
                'last_page' => $paginatedOrganizations->lastPage(),
                'next_page_url' => $paginatedOrganizations->nextPageUrl(),
                'prev_page_url' => $paginatedOrganizations->previousPageUrl(),
            ];
            $organizations = collect($paginatedOrganizations->items());
        }
        return response()->json([
            'data' => $organizations,
            'meta' => $meta,
            'message' => '',
            'status' => true,
            'timestamp' => now()->toIso8601String()
        ]);
    }

}
