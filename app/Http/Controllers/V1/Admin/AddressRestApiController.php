<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdministrativeArea;
use App\Models\Locality;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Validator;
class AddressRestApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/address",
     *     tags={"Address"},
     *     summary="Get Address",
     *     description="Fetch country, province, district, or locality based on selection",
     *     operationId="getAddress",
     *     @OA\Parameter(
     *         name="parent_id",
     *         in="query",
     *         description="For 'province', provide the country ID. For 'district' and 'locality', provide the parent administrative area's ID.",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="level",
     *         in="query",
     *         description="Specify the level: 'country', 'province', 'district', 'locality'",
     *         required=true,
     *         @OA\Schema(type="string", enum={"country", "province", "district", "locality"})
     *     ),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */
    public function getAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|integer',
            'level' => 'required|string|in:country,province,district,locality'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $level = $request->level;
        $parent_id = $request->parent_id;

        switch ($level) {
            case 'country':
                $data = Country::select('id', 'name')->get();
                break;

            case 'province':
                if (!$parent_id) {
                    return response()->json(['error' => 'parent_id is required for province'], 400);
                }
                $data = AdministrativeArea::where('country_id', $parent_id)
                    ->select('id', 'name')
                    ->get();
                break;

            case 'district':
                if (!$parent_id) {
                    return response()->json(['error' => 'parent_id is required for district'], 400);
                }
                $data = AdministrativeArea::where('parent_id', $parent_id)
                    ->select('id', 'name')
                    ->get();
                break;

            case 'locality':
                if (!$parent_id) {
                    return response()->json(['error' => 'parent_id is required for locality'], 400);
                }
                $data = Locality::where('administrative_area_id', $parent_id)
                    ->select('id', 'name')
                    ->get();
                break;

            default:
                return response()->json(['error' => 'Invalid level'], 400);
        }

        return response()->json(['data' => $data], 200);
    }
}
