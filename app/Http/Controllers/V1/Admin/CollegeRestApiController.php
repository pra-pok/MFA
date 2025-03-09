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

class CollegeRestApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/college",
     *     summary="Retrieve Colleges with Pagination",
     *     tags={"College"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (for pagination)",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Limit number of records (alternative to pagination)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="Offset for limited data",
     *         required=false,
     *         @OA\Schema(type="integer", default=0)
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getCollege(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $limit = $request->input('limit');
        $offset = $request->input('offset', 0);
        $query = Organization::orderBy('id', 'desc');
        if ($limit) {
            $total = $query->count();
            $colleges = $query->limit($limit)->offset($offset)->get();
            $meta = [
                'total' => $total,
                'per_page' => (int) $limit,
                'current_page' => (int) ceil(($offset + 1) / $limit),
                'last_page' => (int) ceil($total / $limit),
                'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
            ];
        } else {
            $paginatedColleges = $query->paginate($perPage);
            $meta = [
                'total' => $paginatedColleges->total(),
                'per_page' => $paginatedColleges->perPage(),
                'current_page' => $paginatedColleges->currentPage(),
                'last_page' => $paginatedColleges->lastPage(),
                'next_page_url' => $paginatedColleges->nextPageUrl(),
                'prev_page_url' => $paginatedColleges->previousPageUrl(),
            ];
            $colleges = collect($paginatedColleges->items());
        }
        $colleges->each(function ($college) {
            $college->review_count = Review::where('organization_id', $college->id)->count();
            $college->average_rating = Review::where('organization_id', $college->id)->avg('rating') ?? 0;
            $college->makeHidden([
                'rank', 'stream_id', 'level_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'total_view',
                'locality_id', 'administrative_area_id', 'country_id', 'google_map', 'search_keywords'
            ]);
            $college->logo = !empty($college->logo) ? url('/file/organization/' . $college->logo) : '';
            $college->banner_image = !empty($college->banner_image) ? url('/file/organization_banner/' . $college->banner_image) : '';
        });

        return response()->json([
            'data' => $colleges,
            'meta' => $meta,
            'message' => '',
            'status' => true,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/org/{id}",
     *     summary="Retrieve College Details",
     *     tags={"College"},
     *     description="Fetch detailed information of a college by its ID, including galleries, courses, facilities, reviews, and related news events.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="College ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="college",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="ABC College"),
     *                     @OA\Property(property="slug", type="string", example="abc-college"),
     *                     @OA\Property(property="country", type="string", example="USA"),
     *                     @OA\Property(property="administrative_area", type="string", example="California"),
     *                     @OA\Property(property="district", type="string", example="Los Angeles"),
     *                     @OA\Property(property="locality", type="string", example="Downtown"),
     *                     @OA\Property(property="address", type="string", example="123 College Street"),
     *                     @OA\Property(property="phone", type="string", example="+1 234 567 890"),
     *                     @OA\Property(property="email", type="string", example="info@college.com"),
     *                     @OA\Property(property="website", type="string", example="https://www.college.com"),
     *                     @OA\Property(property="description", type="string", example="A prestigious college offering various courses."),
     *                     @OA\Property(property="type", type="string", example="Private"),
     *                     @OA\Property(property="established_year", type="integer", example=1990),
     *                     @OA\Property(property="google_map", type="string", example="https://maps.google.com/..."),
     *                     @OA\Property(property="view_count", type="integer", example=1500),
     *                     @OA\Property(property="meta_title", type="string", example="Top College in California"),
     *                     @OA\Property(property="meta_keywords", type="string", example="college, university, education"),
     *                     @OA\Property(property="meta_description", type="string", example="ABC College is a top-ranked institution."),
     *                     @OA\Property(property="review_count", type="integer", example=100),
     *                     @OA\Property(property="average_rating", type="number", format="float", example=4.5),
     *                     @OA\Property(
     *                         property="organizationGalleries",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="media", type="string", example="https://example.com/gallery/image.jpg")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="organizationCourses",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="course_title", type="string", example="Computer Science"),
     *                             @OA\Property(property="University_name", type="string", example="Harvard University")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="organizationfacilities",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="facility_title", type="string", example="Library"),
     *                             @OA\Property(property="facility_icon", type="string", example="https://example.com/icon.png")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="organization_new_events",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="thumbnail", type="string", example="https://example.com/news_event.jpg"),
     *                             @OA\Property(property="file", type="string", example="https://example.com/event.pdf")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="review",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="rating", type="number", format="float", example=4.5),
     *                             @OA\Property(property="comment", type="string", example="Great college with excellent faculty!")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="college-list",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="XYZ University"),
     *                     @OA\Property(property="review_count", type="integer", example=50),
     *                     @OA\Property(property="average_rating", type="number", format="float", example=4.0),
     *                     @OA\Property(property="logo", type="string", example="https://example.com/college_logo.jpg"),
     *                     @OA\Property(property="banner_image", type="string", example="https://example.com/banner.jpg")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="next_page_url", type="string", example="https://api.example.com/api/v1/org?page=2"),
     *                 @OA\Property(property="prev_page_url", type="string", example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="College not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="College not found!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid request parameters.")
     *         )
     *     )
     * )
     */

    public function collegeDetail(Request $request, $id)
    {
        $college = Organization::with([
            'organizationGalleries',
            'organizationCourses',
            'organizationPages',
            'organizationsocialMedia',
            'organizationfacilities',
            'locality.administrativeArea.parent',
            'country',
            'organizationNewsEvents'
        ])->find($id);
        if (!$college) {
            return response()->json([
                'message' => 'College not found!',
                'status' => 'error',
            ], 404);
        }
        $reviewCount = Review::where('organization_id', $id)->count();
        $averageRating = Review::where('organization_id', $id)->avg('rating');
        $newsEvents = NewEvent::whereHas('organizations', function ($query) use ($id) {
            $query->where('organization_id', $id);
        })->get();
        $newsEvents->each(function ($event) {
            $event->makeHidden([ 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by']);
            $event->thumbnail = !empty($event->thumbnail) ? url('/file/news_event/' . $event->thumbnail) : '';
            $event->file = !empty($event->file) ? url('/file/news_event_pdf/' . $event->file) : '';
        });
        $review = Review::where('organization_id', $id)->get();
        $review->each(function ($item) {
            $item->makeHidden([
                 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
                'organization_id'
            ]);
        });
        $college->makeHidden([
           'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
            'administrative_area_id'
        ]);
        foreach (['organizationGalleries', 'organizationCourses', 'organizationPages', 'organizationsocialMedia', 'organizationfacilities'] as $relation) {
            if ($college->$relation) {
                $college->$relation->each(function ($item) {
                    $item->makeHidden([
                        'title', 'organization_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'university',
                        'gallery_category_id', 'course_id', 'page_id', 'social_media_id', 'facility_id', 'university_id', 'page_category_id'
                    ]);
                });
            }
        }
        if (!empty($college->organizationGalleries)) {
            foreach ($college->organizationGalleries as $gallery) {
                if ($gallery->type == 1 && !empty($gallery->media)) {
                    $gallery->media = url('/file/organization-gallery/' . $gallery->media);
                } elseif ($gallery->type == 0 && !empty($gallery->media)) {
                    $gallery->media = $gallery->media;
                } else {
                    $gallery->media = null;
                }
                $gallery->gallery_category_name = $gallery->galleryCategory->name ?? '';
                unset($gallery->galleryCategory);
            }
        }

        if (!empty($college->organizationCourses)) {
            foreach ($college->organizationCourses as $course) {
                $course->course_title = $course->course->title ?? '';
                $course->University_name = $course->university->title ?? '';
                unset($course->course);
            }
        }
        if (!empty($college->organizationPages)) {
            foreach ($college->organizationPages as $page) {
                $page->page_title = $page->page->title ?? '';
                unset($page->page);
            }
        }
        if (!empty($college->organizationfacilities)) {
            foreach ($college->organizationfacilities as $facility) {
                $facility->facility_title = $facility->facility->title ?? '';
                $facility->facility_icon = $facility->facility->icon ?? '';
                unset($facility->facility);
            }
        }
        $responseData = [
            'id' => $college->id,
            'country' => $college->country->name ?? '',
            'administrative_area' => $college->locality->administrativeArea->parent->name ?? '',
            'District' => $college->locality->administrativeArea->name ?? '',
            'Locality' => $college->locality->name ?? '',
            'name' => $college->name,
            'slug' => $college->slug,
            'logo' => !empty($college->logo) ? url('/file/organization/' . $college->logo) : '',
            'banner_image' => !empty($college->banner_image) ? url('/file/organization_banner/' . $college->banner_image) : '',
            'address' => $college->address,
            'phone' => $college->phone,
            'email' => $college->email,
            'website' => $college->website,
            'description' => $college->description,
            'type' => $college->type,
            'established_year' => $college->established_year,
            'google_map' => $college->google_map,
            'view_count' => $college->increment('total_view'),
            'search_keywords' => $college->search_keywords,
            'meta_title' => $college->meta_title,
            'meta_keywords' => $college->meta_keywords,
            'meta_description' => $college->meta_description,
            'organizationGalleries' => $college->organizationGalleries,
            'organizationCourses' => $college->organizationCourses,
            'organizationPages' => $college->organizationPages,
            'organizationsocialMedia' => $college->organizationsocialMedia,
            'organizationfacilities' => $college->organizationfacilities,
            'review_count' => $reviewCount,
            'average_rating' => round($averageRating, 1) ?? 0,
            'organization_new_events' => $newsEvents,
            'review' => $review,
        ];
        $perPage = $request->input('per_page', 10);
        $limit = $request->input('limit');
        $offset = $request->input('offset', 0);
        $query = Organization::orderBy('id', 'desc');
        if ($limit) {
            $total = $query->count();
            $colleges = $query->limit($limit)->offset($offset)->get();
            $meta = [
                'total' => $total,
                'per_page' => (int) $limit,
                'current_page' => (int) ceil(($offset + 1) / $limit),
                'last_page' => (int) ceil($total / $limit),
                'next_page_url' => ($offset + $limit < $total) ? url()->current() . "?limit=$limit&offset=" . ($offset + $limit) : null,
                'prev_page_url' => ($offset - $limit >= 0) ? url()->current() . "?limit=$limit&offset=" . ($offset - $limit) : null,
            ];
        } else {
            $paginatedColleges = $query->paginate($perPage);
            $meta = [
                'total' => $paginatedColleges->total(),
                'per_page' => $paginatedColleges->perPage(),
                'current_page' => $paginatedColleges->currentPage(),
                'last_page' => $paginatedColleges->lastPage(),
                'next_page_url' => $paginatedColleges->nextPageUrl(),
                'prev_page_url' => $paginatedColleges->previousPageUrl(),
            ];
            $colleges = collect($paginatedColleges->items());
        }
        $colleges->each(function ($college) {
            $college->review_count = Review::where('organization_id', $college->id)->count();
            $college->average_rating = Review::where('organization_id', $college->id)->avg('rating') ?? 0;
            $college->makeHidden([
                'rank', 'stream_id', 'level_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
                'meta_title', 'meta_keywords', 'meta_description', 'total_view',
                'locality_id', 'administrative_area_id', 'country_id', 'google_map', 'search_keywords'
            ]);
            $college->logo = !empty($college->logo) ? url('/file/organization/' . $college->logo) : '';
            $college->banner_image = !empty($college->banner_image) ? url('/file/organization_banner/' . $college->banner_image) : '';
        });

        return response()->json([
            'message' => '',
            'status' => 'success',
            'data' => [
                'college' => $responseData
            ],
            'college-list' => $colleges,
            'meta' => $meta,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/news/event/{id}",
     *     summary="Get details of a specific News Event",
     *     tags={"News Event"},
     *     description="Fetch details of a specific News Event by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the News Event",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example=""),
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="news-event",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="News Event Title"),
     *                     @OA\Property(property="description", type="string", example="Detailed description of the event"),
     *                     @OA\Property(property="thumbnail", type="string", example="https://example.com/file/news_event/image.jpg"),
     *                     @OA\Property(property="file", type="string", example="https://example.com/file/news_event_pdf/document.pdf")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="News Event not found"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function news_events($id)
    {
        $data['news-event'] = NewEvent::find($id);
        if (!$data['news-event']) {
            return response()->json([
                'message' => 'News Event not found!',
                'status' => 'error',
            ], 404);
        }
        $data['news-event']->makeHidden([
             'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'
        ]);
        $data['news-event']->thumbnail = $data['news-event']->thumbnail ? url('/file/news_event/' . $data['news-event']->thumbnail) : '';
        $data['news-event']->file = $data['news-event']->file ? url('/file/news_event/' . $data['news-event']->file) : '';
        return response()->json([
            'message' => '',
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
}
