<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
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
            ->select('id', 'name as organization_name', 'short_name', 'slug')
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
            $courses = collect($courses->items());
            $universities = collect($universities->items());
            $organizations = collect($organizations->items());
        }

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

   public function getSimpleSearch(Request $request)
   {
       $search = $request->query('keyword', '');
       $type = $request->query('type', 'simple');
       $perPage = (int) $request->query('per_page', 10);
       $limit = $request->query('limit');
       $offset = (int) $request->query('offset', 0);

   }

}
