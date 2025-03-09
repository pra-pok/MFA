<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationSignup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class CollegeLoginApiController extends Controller
{
    /**
     * @OA\SecurityScheme(
     *     securityScheme="Bearer",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="JWT Bearer Authentication"
     * )
     */
    /**
     * College Login
     * @OA\Post (
     *     path="/api/v1/college/login",
     *     tags={"College"},
     *     summary="College Login API",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="username",
     *                          type="string"
     *                      ),
     *                     @OA\Property(
     *                         property="password",
     *                        type="string"
     *                    ),
     *                 ),
     *                 example={
     *                     "username":"example username",
     *                     "password":"example password",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string", example="JWT_TOKEN_HERE"),
     *              @OA\Property(property="message", type="string", example="Login Success"),
     *              @OA\Property(property="status", type="string", example="success"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid Credentials"),
     *              @OA\Property(property="status", type="string", example="failed"),
     *          )
     *      )
     * )
     */
//    public function collegeLogin(Request $request)
//    {
//        $request->validate([
//            'username' => 'required|string',
//            'password' => 'required|string',
//        ]);
//        $college = OrganizationSignup::where('username', $request->username)->first()
//            ->makeHidden(['status','created_at', 'updated_at', 'deleted_at','created_by','updated_by','comment',]);
//        if (!$college || !Hash::check($request->password, $college->password)) {
//            return response()->json([
//                'message' => 'Invalid Credentials',
//                'status' => 'failed'
//            ], 401);
//        }
//        try {
//            $token = JWTAuth::fromUser($college);
//        } catch (JWTException $e) {
//            return response()->json([
//                'message' => 'Could not create token',
//                'status' => 'failed'
//            ], 500);
//        }
//        return response()->json([
//            'token' => $token,
//            'data' => $college,
//            'message' => 'Login Success',
//            'status' => 'success'
//        ], 200);
//    }
    public function collegeLogin(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            // Fetch college record
            $college = OrganizationSignup::where('username', $request->username)->first();
            // Check credentials
            if (!$college || !Hash::check($request->password, $college->password)) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Invalid credentials',
                    'data' => null
                ], 401);
            }
            // Generate token
            $token = JWTAuth::fromUser($college);

            // Hide sensitive fields
            $college->makeHidden([
                'status',
                'created_at',
                'updated_at',
                'deleted_at',
                'created_by',
                'updated_by',
                'comment'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'token' => $token,
                    'college' => $college
                ]
            ], 200);

        } catch (JWTException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Could not create token',
                'data' => null
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'An error occurred during login',
                'data' => null
            ], 500);
        }
    }
}
