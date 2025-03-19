<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmsLog;
use App\Models\SmsApiToken;
use App\Jobs\SendSmsJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Logging\Log;
use App\Models\OrganizationSignup;

class SmsApiController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/v1/sms/api/token",
     *     security={{ "Bearer": { }}},
     *     summary="Store API token for SMS Vendor",
     *     description="Stores the API token for a specific SMS vendor",
     *     operationId="storeApiToken",
     *     tags={"SMS API"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vendor", "identity", "token"},
     *             @OA\Property(property="vendor", type="string", example="Twilio"),
     *             @OA\Property(property="identity", type="string", example="my_app_id"),
     *             @OA\Property(property="token", type="string", example="abcdef12345")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="API token saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="API token saved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function storeApiToken(Request $request)
    {
        $request->validate([
            'vendor' => 'required|string',
            'identity' => 'required|string',
            'token' => 'required|string',
        ]);

        $apiToken = SmsApiToken::create([
            'vendor' => $request->vendor,
            'identity' => $request->identity,
            'token' => $request->token,
        ]);

        return response()->json(['message' => 'API token saved successfully', 'data' => $apiToken], 201);
    }
    /**
     * @OA\Post(
     *     path="/api/v1/sms/send",
     *     security={{ "Bearer": { }}},
     *     summary="Send SMS to multiple recipients",
     *     description="Send SMS using a vendor API token. Multiple recipients can be sent by passing an array of phone numbers.",
     *     operationId="sendSms",
     *     tags={"SMS API"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"recipients", "message", "sender_phone_number", "vendor"},
     *             @OA\Property(
     *                 property="recipients",
     *                 type="array",
     *                 @OA\Items(type="string", example="+1234567890"),
     *                 example={"+1234567890", "+0987654321"}
     *             ),
     *             @OA\Property(property="message", type="string", example="Your OTP is 123456"),
     *             @OA\Property(property="vendor", type="string", example="vendor123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="SMS is being processed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="SMS is being processed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Vendor not found or invalid data"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function sendSms(Request $request)
    {
        $request->validate([
            'recipients' => 'required|array',  // ✅ Allow an array instead of string
            'recipients.*' => 'required|string|regex:/^\+?\d{10,15}$/', // ✅ Ensure each number is valid
            'message' => 'required|string',
            'sender_phone_number' => 'required|string',
            'vendor' => 'required|string|exists:sms_api_tokens,vendor',
        ]);
    
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        $apiToken = SmsApiToken::where('vendor', $request->vendor)->first();
        if (!$apiToken) {
            return response()->json(['error' => 'Invalid vendor'], 400);
        }
    
        foreach ($request->recipients as $recipient) {
            $smsLog = SmsLog::create([
                'vendor' => $apiToken->vendor,
                'recipients' => $recipient,  // ✅ Save each number separately
                'message' => $request->message,
                'sender_phone_number' => $user->phone,
                'status' => 'pending',
                'sms_api_token_id' => $apiToken->id,
                'organization_id' => $user->id,
                'organization_name' => $user->username,
                'response' => null,
            ]);
    
            dispatch(new SendSmsJob(
                $recipient, // ✅ Send one number at a time
                $request->message,
                $apiToken->token,
                $smsLog->id
            ));
        }
    
        return response()->json([
            'message' => 'SMS is being processed via queue'
        ], 200);
    }
}
