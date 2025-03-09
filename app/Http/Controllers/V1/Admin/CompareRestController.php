<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Course;
use App\Models\Level;
use App\Models\NewEvent;
use App\Models\Organization;
use App\Models\Review;
use App\Models\Stream;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;
use OpenApi\Annotations as OA;
class CompareRestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/college/compare",
     *     summary="Compare multiple colleges by ID",
     *     tags={"College"},
     *     description="Fetch and compare details of multiple colleges based on their IDs.",
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Comma-separated list of College IDs",
     *         required=true,
     *         @OA\Schema(type="string", example="1,2,3")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="College Name")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=404, description="No colleges found")
     * )
     */
    public function Collegecompare(Request $request)
    {
        $collegeIds = $request->query('id', '');
        $idsArray = array_filter(explode(',', $collegeIds), 'is_numeric');
        if (empty($idsArray)) {
            return response()->json([
                "message" => "Please provide valid college IDs",
                "status" => false
            ], 400);
        }
        $colleges = Organization::with([
            'organizationCourses.course',
            'organizationFacilities.facility',
            'organizationGalleries.galleryCategory',
            'organizationPages.page'
        ])
            ->whereIn('id', $idsArray)
            ->select('id', 'name', 'logo', 'banner_image','address', 'type', 'established_year', 'phone', 'email', 'website', 'google_map', 'description',
            'search_keywords', 'meta_title', 'meta_description', 'meta_keywords')
            ->get();
        if ($colleges->isEmpty()) {
            return response()->json([
                "message" => "No colleges found for the given IDs",
                "status" => false
            ], 404);
        }

        $formattedColleges = [];
        foreach ($colleges as $college) {
            $college->review_count = Review::where('organization_id', $college->id)->count();
            $college->average_rating = Review::where('organization_id', $college->id)->avg('rating') ?? 0;
            foreach (['organizationGalleries', 'organizationCourses', 'organizationPages', 'organizationFacilities', 'organizationReviews'] as $relation) {
                if ($college->$relation) {
                    $college->$relation->each(function ($item) {
                        $item->makeHidden([
                            'organization_id', 'rank', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'university',
                            'gallery_category_id', 'course_id', 'page_id', 'social_media_id', 'facility_id', 'university_id', 'page_category_id'
                        ]);
                    });
                }
            }
            $galleries = [];
            if ($college->organizationGalleries) {
                foreach ($college->organizationGalleries as $gallery) {
                    $galleries[] = [
                        "gallery_category" => $gallery->galleryCategory->name ?? '',
                        "caption" => $gallery->caption ?? '',
                        "type" => (string) $gallery->type ?? '0',
                        "media" => (!empty($gallery->media) && $gallery->type == 1)
                            ? url('/file/organization-gallery/' . $gallery->media)
                            : $gallery->media ?? null,

                    ];
                }
            }
            $courses = [];
            if ($college->organizationCourses) {
                foreach ($college->organizationCourses as $course) {
                    $courses[] = [
                        "course" => $course->course->title ?? '',
                        "university" => $course->university->title ?? '',
                        "start_fee" => $course->start_fee ?? '',
                        "end_fee" => $course->end_fee ?? '',
                        "description" => $course->description ?? '',
                    ];
                }
            }
            $pages = [];
            if ($college->organizationPages) {
                foreach ($college->organizationPages as $page) {
                    $pages[] = [
                        "page_title" => $page->page->title ?? '',
                        "description" => $page->description ?? ''
                    ];
                }
            }
            $facilities = [];
            if ($college->organizationFacilities) {
                foreach ($college->organizationFacilities as $facility) {
                    $facilities[] = [
                        "title" => $facility->facility->title ?? '',
                        "icon" => $facility->facility->icon ?? ''
                    ];
                }
            }
            $formattedColleges[] = [
                "id" => $college->id,
                "name" => $college->name,
                'logo' => !empty($college->logo) ? url('/file/organization/' . $college->logo) : '',
                'banner_image' => !empty($college->banner_image) ? url('/file/organization_banner/' . $college->banner_image) : '',
                "address" => $college->address,
                "type" => $college->type,
                "established_year" => $college->established_year,
                "phone" => $college->phone,
                "email" => $college->email,
                "website" => $college->website,
                "google_map" => $college->google_map,
                "description" => $college->description,
                "search_keywords" => $college->search_keywords,
                "meta_title" => $college->meta_title,
                "meta_description" => $college->meta_description,
                "meta_keywords" => $college->meta_keywords,
                "organizationGalleries" => $galleries,
                "organizationCourses" => $courses,
                "organizationPages" => $pages,
                "organizationfacilities" => $facilities,
                "review_count" => $college->review_count,
                "average_rating" => $college->average_rating,
            ];
        }
        return response()->json([
            "data" => $formattedColleges,
            "message" => "",
            "status" => true,
            "timestamp" => now()->toIso8601String()
        ]);
    }

}
