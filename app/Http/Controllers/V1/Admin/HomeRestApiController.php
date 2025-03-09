<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeArea;
use App\Models\Catalog;
use App\Models\Country;
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
class HomeRestApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/home",
     *     summary="Get a list of home page data",
     *     tags={"Home"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function index(Request $request)
    {
        try {
            $data['catalog'] = [];
            $catalogs = DB::select("
            SELECT c.id, c.title, c.type
            FROM catalogs c
            WHERE c.type = 'College'
        ");
            if (empty($catalogs)) {
                return Utils\ResponseUtil::wrapResponse(
                    new ResponseDTO(null, 'No College catalogs found.', 'error', 404)
                );
            }
            foreach ($catalogs as $catalog) {
                $organizations = DB::select("
                SELECT o.id, o.name, o.slug, o.address, o.email, o.phone,
                       o.website, o.established_year, o.type, o.description, o.logo , o.banner_image, o.google_map,
                       o.search_keywords, o.meta_title, o.meta_keywords, o.meta_description
                FROM organizations o
                JOIN organization_catalogs oc ON o.id = oc.organization_id
                WHERE oc.catalog_id = ?", [$catalog->id]);
                $data['catalog'][] = [
                    'id' => $catalog->id,
                    'title' => $catalog->title,
                    'type' => $catalog->type,
                    'data' => array_map(function ($org) {
                        $reviewCount = DB::table('reviews')->where('organization_id', $org->id)->count();
                        $averageRating = DB::table('reviews')->where('organization_id', $org->id)->avg('rating');

                        return [
                            'id' => $org->id,
                            'name' => $org->name,
                            'logo' => !empty($org->logo)
                                ? url('/file/organization/' . $org->logo)
                                : null,
                            'slug' => $org->slug,
                            'address' => $org->address,
                            'email' => $org->email,
                            'phone' => $org->phone,
                            'website' => $org->website,
                            'established_year' => $org->established_year,
                            'banner_image' => !empty($org->banner_image)
                                ? url('/file/organization_banner/' . $org->banner_image)
                                : null,
                            'type' => $org->type,
                            'description' => $org->description,
                            'google_map' => $org->google_map,
                            'review_count' => $reviewCount,
                            'average_rating' => $averageRating ? round($averageRating, 2) : 0,
                            'search_keywords' => $org->search_keywords,
                            'meta_title' => $org->meta_title,
                            'meta_keywords' => $org->meta_keywords,
                            'meta_description' => $org->meta_description,
                        ];
                    }, $organizations)
                ];
            }
            $catalog_course = DB::select("
            SELECT cc.id, cc.title, cc.type
            FROM catalogs cc
            WHERE cc.type = 'Course'
        ");
            if (empty($catalog_course)) {
                return Utils\ResponseUtil::wrapResponse(
                    new ResponseDTO(null, 'No Course catalogs found.', 'error', 404)
                );
            }
            foreach ($catalog_course as $catalog) {
                $courses = DB::select("
                SELECT c.id, c.title, c.slug, c.short_title, c.eligibility, c.job_prospects,
                       c.syllabus, c.description, c.stream_id, c.level_id, c.duration,c.min_range_fee ,c.max_range_fee ,
                       c.meta_title,c.meta_keywords,c.meta_description,s.title AS stream_title,
                       l.title AS level_title
                FROM courses c
                JOIN course_catalogs ccc ON c.id = ccc.course_id
                LEFT JOIN streams s ON c.stream_id = s.id
                LEFT JOIN levels l ON c.level_id = l.id
                WHERE ccc.catalog_id = ?", [$catalog->id]);
                $data['catalog'][] = [
                    'id' => $catalog->id,
                    'title' => $catalog->title,
                    'type' => $catalog->type,
                    'data' => array_map(function ($course) {
                        return [
                            'id' => $course->id,
                            'stream_id' => $course->stream_id,
                            'stream_title' => $course->stream_title,
                            'level_id' => $course->level_id,
                            'level_title' => $course->level_title,
                            'title' => $course->title,
                            'slug' => $course->slug,
                            'short_title' => $course->short_title,
                            'eligibility' => $course->eligibility,
                            'job_prospects' => $course->job_prospects,
                            'syllabus' => $course->syllabus,
                            'description' => $course->description,
                            'duration' => $course->duration,
                            'min_range_fee' => $course->min_range_fee,
                            'max_range_fee' => $course->max_range_fee,
                            'meta_title' => $course->meta_title,
                            'meta_keywords' => $course->meta_keywords,
                            'meta_description' => $course->meta_description,
                        ];
                    }, $courses)
                ];
            }
            $country = $request->input('country');
            $catalog_university = DB::select("
            SELECT c.id, c.title, c.type
            FROM catalogs c
            WHERE c.type = 'University'
        ");
            if (empty($catalog_university)) {
                return Utils\ResponseUtil::wrapResponse(
                    new ResponseDTO(null, 'No University catalogs found.', 'error', 404)
                );
            }
            foreach ($catalog_university as $catalog) {
                $university = DB::select("
                SELECT u.id, u.title, u.slug, u.types, u.logo, u.description, c.meta_title,c.meta_keywords,c.meta_description
                FROM universities u
                JOIN university_catalogs uc ON u.id = uc.university_id
                JOIN countries c ON u.country_id = c.id
                WHERE uc.catalog_id = ? AND c.iso_code = ?
            ", [$catalog->id, $country]);
                $data['catalog'][] = [
                    'id' => $catalog->id,
                    'title' => $catalog->title,
                    'type' => $catalog->type,
                    'data' => array_map(function ($university) {
                        return [
                            'id' => $university->id,
                            'title' => $university->title,
                            'slug' => $university->slug,
                            'types' => $university->types,
                            'logo' => !empty($university->logo)
                                ? url('/file/university/' . $university->logo)
                                : null,
                            'description' => $university->description,
                            'meta_title' => $university->meta_title,
                            'meta_keywords' => $university->meta_keywords,
                            'meta_description' => $university->meta_description,
                        ];
                    }, $university)
                ];
            }
            $data['news'] = NewEvent::where('status', 1)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->makeHidden([
                    'id', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'
                ]);

            $data['news']->transform(function ($item) {
                $item->thumbnail = $item->thumbnail ? url('/file/news_event/' . $item->thumbnail) : '';
                return $item;
            });
            $data['news']->transform(function ($item) {
                $item->file = $item->file ? url('/file/news_event_pdf/' . $item->file) : '';
                return $item;
            });
            $data['country'] = Country::where('status' , 1)->get()->makeHidden([
                'id','rank', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'
            ]);

            $data['place'] = AdministrativeArea::where('status' , 1)->whereNotNull('parent_id')
                ->get()->makeHidden([
                'country_id','rank', 'status', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'
            ]);

            return Utils\ResponseUtil::wrapResponse(
                new ResponseDTO($data, '', 'success', 200)
            );
        } catch (\Exception $e) {
            // Handle unexpected errors
            return Utils\ResponseUtil::wrapResponse(
                new ResponseDTO(null, 'An error occurred: ' . $e->getMessage(), 'error', 500)
            );
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/org/review",
     *     tags={"Review"},
     *     summary="Store a review for an organization",
     *     description="Create a review for an organization. It checks for existing reviews from the same user for the same organization.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "organization_id"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name of the reviewer"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Email of the reviewer"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     description="Phone number of the reviewer"
     *                 ),
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     description="Review message"
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="integer",
     *                     description="Rating given by the reviewer (1-5)"
     *                 ),
     *                 @OA\Property(
     *                     property="organization_id",
     *                     type="integer",
     *                     description="ID of the organization being reviewed"
     *                 ),
     *                 example={
     *                     "name":"John Doe",
     *                     "email":"johndoe@example.com",
     *                     "phone":"1234567890",
     *                     "message":"Great organization!",
     *                     "rating":5,
     *                     "organization_id":1
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review successfully stored",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Review added successfully!"),
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="fail")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Review already exists from this user for the same organization",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="A review from this user for this organization already exists!"),
     *             @OA\Property(property="status", type="string", example="error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred: {error message}"),
     *             @OA\Property(property="status", type="string", example="error")
     *         )
     *     )
     * )
     */
    public function reviewStore(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'organization_id' => 'required|integer',
            ]);
            $existingReview = Review::where('name', $request->name)
                ->where('email', $request->email)
                ->where('organization_id', $request->organization_id)
                ->first();
            if ($existingReview) {
                return response()->json([
                    'message' => 'A review from this user for this organization already exists!',
                    'status' => 'error',
                ], 409);
            }
            $review = new Review();
            $review->name = $request->name;
            $review->email = $request->email;
            $review->phone = $request->phone;
            $review->message = $request->message;
            $review->rating = $request->rating;
            $review->organization_id = $request->organization_id;
            $review->save();
            return response()->json([
                'message' => 'Review added successfully!',
                'status' => 'success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status' => 'error',
            ], 500);
        }

    }
    /**
     * @OA\Get(
     *     path="/api/v1/menu",
     *     summary="Get a list of header and footer menu items",
     *     description="Get a list of menu items for the header and footer of the website.",
     *     tags={"Home"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function menu()
    {
        $data = [
            'data' => [
                'header' => [
                    'menu' => [
                        'home' => '/',
                        'colleges' => '/colleges',
                        'universities' => '/universities',
                        'courses' => '/courses',
                        'news' => '/news',
                        'events' => '/events',
                        'about_us' => '/about-us',
                        'contact_us' => '/contact-us',
                    ]
                ],
                'footer' => [
                    'section' => [
                        [
                            'title' => 'Information',
                            'items' => [
                                'logo' => 'https://edigitalnepal.edu.np/images/logo.png',
                                'short_description' => 'eDigital Nepal is a platform that provides information about educational institutions, courses, and events in Nepal.',
                                'social_media' => [
                                    [
                                        'name' => 'facebook',
                                        'link' => 'https://www.facebook.com/edigitalnepal',
                                        'target' => '_blank'
                                    ],
                                    [
                                        'name' => 'twitter',
                                        'link' => 'https://twitter.com/edigitalnepal',
                                        'target' => '_blank'
                                    ],
                                    [
                                        'name' => 'instagram',
                                        'link' => 'https://www.instagram.com/edigitalnepal',
                                        'target' => '_blank'
                                    ],
                                    [
                                        'name' => 'linkedin',
                                        'link' => 'https://www.linkedin.com/company/edigitalnepal',
                                        'target' => '_blank'
                                    ],
                                    [
                                        'name' => 'youtube',
                                        'link' => 'https://www.youtube.com/channel/UC',
                                        'target' => '_blank'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'Useful Links',
                            'items' => [
                                [
                                    'name' => 'About Us',
                                    'link' => '',
                                    'target' => '_blank'
                                ],
                                [
                                    'name' => 'Contact Us',
                                    'link' => '',
                                    'target' => '_blank'
                                ],
                                [
                                    'name' => 'Privacy Policy',
                                    'link' => '',
                                    'target' => '_blank'
                                ],
                                [
                                    'name' => 'Terms of Use',
                                    'link' => '',
                                    'target' => '_blank'
                                ]
                            ]
                        ],
                        [
                            'title' => 'Get In Touch',
                            'items' => [
                                'email' => '',
                                'address' => 'Samudayeek Marg - 32, Tinkune, Kathmandu, Nepal'
                            ]
                        ],
                        [
                            'title' => 'Support & Information',
                            'items' => [
                                'phone' => '+977-1-411 0000',
                                'email' => '',
                            ]
                        ],
                        [
                            'title' => 'Sales & Marketing',
                            'items' => [
                                'phone' => '+977-1-411 0000',
                                'email' => '',
                                'Find us on Google Map' => [
                                    'link' => 'https://goo.gl/maps/1',
                                    'target' => '_blank'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return response()->json($data);
    }
}
