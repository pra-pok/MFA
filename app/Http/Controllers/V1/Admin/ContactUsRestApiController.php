<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Dtos\ResponseDTO;
use App\Utils;
use OpenApi\Annotations as OA;
class ContactUsRestApiController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/contactus/review",
     *     tags={"Contact Us"},
     *     summary="Store a review from a user",
     *     description="Store a review from a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name", "email", "phone", "message"},
     *                 @OA\Property(property="name", type="string", description="Name of the reviewer"),
     *                 @OA\Property(property="email", type="string", description="Email of the reviewer"),
     *                 @OA\Property(property="phone", type="string", description="Phone number of the reviewer"),
     *                 @OA\Property(property="message", type="string", description="Review message"),
     *                 example={
     *                     "name": "John Doe",
     *                     "email": "johndoe@example.com",
     *                     "phone": "1234567890",
     *                     "message": "This is a review message"
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
     *             @OA\Property(property="message", type="string", example="Invalid input data"),
     *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}}),
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
    public function contactStore(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'message' => 'required|string',
            ]);
            $review = new ContactUs();
            $review->name = $request->name;
            $review->email = $request->email;
            $review->phone = $request->phone;
            $review->message = $request->message;
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
}
