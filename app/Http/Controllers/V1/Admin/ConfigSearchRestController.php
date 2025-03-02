<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;

class ConfigSearchRestController extends Controller
{
    public function getConfigSearch(Request $request)
    {
        $data['course'] = DB::table('courses')
            ->select('id', 'title', 'short_title', DB::raw("'course' as type"))
            ->get();
        $data['university'] = DB::table('universities')
            ->select('id', 'title', DB::raw("'university' as type"))
            ->get();
        $data['type'] = ['1' => 'Public', '2' => 'Private', '3' => 'Community (Aided)'
            , '4' => 'Community (Managed)', '5' => 'Community (Teacher Aid)', '6' => 'Community (Unaided)'
            , '7' => 'Institutional (Private)', '8' => 'Institutional (Public)', '9' => 'Institutional (Company)'
            , '10' => 'Public with religious', '11' => 'Madrasa', '12' => 'Gumba', '13' => 'Ashram',
            '14' => 'SOP/FSP', '15' => 'Community ECD', '16' => 'Other'];
        $data['duration'] = ['1' => '1 Year', '2' => '2 Year', '3' => '3 Year', '4' => '4 Year', '5' => '5 Year'];
        $data['average_fee'] =
//        $data['organization'] = DB::table('organizations')
//            ->select('id', 'name as title', DB::raw("'organization' as type"))
//            ->get();

        return response()->json([
            'data' => $data,
            'message' => '',
            'status' => true,
            'timestamp' => now()->toIso8601String()
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
