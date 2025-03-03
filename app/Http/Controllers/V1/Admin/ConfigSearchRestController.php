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
        $organizations = $query->paginate($perPage);
        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $organizations->total(),
                'per_page' => $organizations->perPage(),
                'current_page' => $organizations->currentPage(),
                'last_page' => $organizations->lastPage(),
                'next_page_url' => $organizations->nextPageUrl(),
                'prev_page_url' => $organizations->previousPageUrl(),
            ],
            'message' => '',
            'status' => true,
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
