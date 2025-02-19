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

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="My API",
 *      description="API documentation",
 *      @OA\Contact(
 *          email="support@example.com"
 *      )
 * )
 */
class CollegeRestApiController extends Controller
{
    public function getCollege(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $limit = $request->input('limit');
        $offset = $request->input('offset', 0);
        $query = Organization::orderBy('id', 'desc');
        if ($limit) {
            $total = $query->count();
            $colleges = $query->limit($limit)->offset($offset)->get();
            $pagination = [
                'total' => $total,
                'limit' => (int) $limit,
                'offset' => (int) $offset,
                'next_offset' => $offset + $limit < $total ? $offset + $limit : null,
                'prev_offset' => $offset - $limit >= 0 ? $offset - $limit : null,
            ];
        } else {
            $colleges = $query->paginate($perPage);
            $pagination = [
                'total' => $colleges->total(),
                'per_page' => $colleges->perPage(),
                'current_page' => $colleges->currentPage(),
                'last_page' => $colleges->lastPage(),
                'next_page_url' => $colleges->nextPageUrl(),
                'prev_page_url' => $colleges->previousPageUrl(),
            ];
        }
        $colleges->each(function ($college) {
            $college->makeHidden([
                'id', 'rank', 'stream_id', 'level_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
                'meta_title', 'meta_keywords', 'meta_description', 'total_view',
                    'locality_id','administrative_area_id','country_id','google_map','search_keywords'
            ]);
            $college->logo = !empty($college->logo) ? url('/file/organization/' . $college->logo) : '';
            $college->banner_image = !empty($college->banner_image) ? url('/file-banner/organization/' . $college->banner_image) : '';
        });
        return Utils\ResponseUtil::wrapResponse(new ResponseDTO([
            'data' => $colleges,
            'pagination' => $pagination
        ], '', 'success', 200));
    }
    public function collegeDetail($id)
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
            $event->makeHidden(['id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by']);
            $event->thumbnail = !empty($event->thumbnail) ? url('/file/news_event/' . $event->thumbnail) : '';
            $event->file = !empty($event->file) ? url('/pdf-file/news_event/' . $event->file) : '';
        });
        $college->makeHidden([
            'id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
            'total_view', 'meta_title', 'meta_keywords', 'meta_description', 'search_keywords', 'administrative_area_id'
        ]);
        foreach (['organizationGalleries', 'organizationCourses', 'organizationPages', 'organizationsocialMedia', 'organizationfacilities'] as $relation) {
            if ($college->$relation) {
                $college->$relation->each(function ($item) {
                    $item->makeHidden([
                        'id', 'title', 'organization_id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'university',
                        'gallery_category_id', 'course_id', 'page_id', 'social_media_id', 'facility_id', 'university_id', 'page_category_id'
                    ]);
                });
            }
        }
        if (!empty($college->organizationGalleries)) {
            foreach ($college->organizationGalleries as $gallery) {
                $gallery->media = ($gallery->type == 1 && !empty($gallery->media))
                    ? url('/file-organization/' . $gallery->media)
                    : null;
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
            'country' => $college->country->name ?? '',
            'administrative_area' => $college->locality->administrativeArea->parent->name ?? '',
            'District' => $college->locality->administrativeArea->name ?? '',
            'Locality' => $college->locality->name ?? '',
            'name' => $college->name,
            'slug' => $college->slug,
            'logo' => !empty($college->logo) ? url('/file/organization/' . $college->logo) : '',
            'banner_image' => !empty($college->banner_image) ? url('/file-banner/organization/' . $college->banner_image) : '',
            'address' => $college->address,
            'phone' => $college->phone,
            'email' => $college->email,
            'website' => $college->website,
            'description' => $college->description,
            'type' => $college->type,
            'established_year' => $college->established_year,
            'google_map' => $college->google_map,
            'organizationGalleries' => $college->organizationGalleries,
            'organizationCourses' => $college->organizationCourses,
            'organizationPages' => $college->organizationPages,
            'organizationsocialMedia' => $college->organizationsocialMedia,
            'organizationfacilities' => $college->organizationfacilities,
            'review_count' => $reviewCount,
            'average_rating' => round($averageRating, 1) ?? 0,
            'organization_new_events' => $newsEvents,
        ];
        return response()->json([
            'message' => '',
            'status' => 'success',
            'data' => [
                'college' => $responseData
            ],
        ], 200);
    }

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
            'id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by',
            'meta_title', 'meta_keywords', 'meta_description',
        ]);
        $data['news-event']->thumbnail = $data['news-event']->thumbnail ? url('/file/news_event/' . $data['news-event']->thumbnail) : '';
        $data['news-event']->file = $data['news-event']->file ? url('/pdf-file/news_event/' . $data['news-event']->file) : '';
        return response()->json([
            'message' => '',
            'status' => 'success',
            'data' => $data,
        ], 200);
    }
}
