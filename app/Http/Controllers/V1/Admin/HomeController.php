<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Course;
use App\Models\Level;
use App\Models\Organization;
use App\Models\Stream;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
class HomeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/home",
     *     summary="Get home data",
     *     tags={"Home"},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Welcome to Home API")
     *         )
     *     )
     * )
     */
    public function index()
    {
      //  $data['home'] = Catalog::where('type', 'College')->with('organization')->get();
//        $data['university'] = University::where('status', 1)->get();
//        $data['course'] = Course::where('status', 1)->get();
//        $data['stream'] = Stream::where('status', 1)->get();
//        $data['level'] = Level::where('status', 1)->get();
//        $data['college'] = Organization::where('status', 1)->get();

        $data['catalog'] = [];

        $catalogs = DB::select("
    SELECT c.id, c.title
    FROM catalogs c
    WHERE c.type = 'College'
");

        foreach ($catalogs as $catalog) {
            $organizations = DB::select("
        SELECT o.id, o.name, o.slug, o.address, o.email, o.phone,
               o.website, o.established_year, o.type, o.description, o.logo , o.google_map
        FROM organizations o
        JOIN organization_catalogs oc ON o.id = oc.organization_id
        WHERE oc.catalog_id = ?", [$catalog->id]);

            // Formatting the catalog with nested organization data
            $data['catalog'][] = [
                'id'   => $catalog->id,
                'name' => $catalog->title,

                'data' => array_map(function ($org)  {
                    return [
                        'id'                => $org->id,
                        'name'              => $org->name,
                        'logo'              => !empty($org->logo)
                            ? url('/file/organization'  . '/' . $org->logo)
                            : null,
                        'slug'              => $org->slug,
                        'address'           => $org->address,
                        'email'             => $org->email,
                        'phone'             => $org->phone,
                        'website'           => $org->website,
                        'established_year'  => $org->established_year,
                        'type'              => $org->type,
                        'description'       => $org->description,
                        'google_map'        => $org->google_map,
                    ];
                }, $organizations)
            ];
        }
        return response()->json(['data' => $data]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
