<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmsLog;
use App\Models\SmsApiToken;
use App\Jobs\SendSmsJob;
use Illuminate\Support\Facades\Auth;

class SmsApiController extends Controller
{
     
/**
     * @OA\Post(
     *     path="/api/v1/sms/api/token",
     *     security={{"Bearer": {}}},
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
     *     path="/api/v1/send/sms",
     *     security={{"Bearer": {}}},
     *     summary="Send SMS to a recipient",
     *     description="Send SMS to a specified recipient using a vendor API token",
     *     operationId="sendSms",
     *     tags={"SMS API"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"recipient", "message", "sender", "vendor"},
     *             @OA\Property(property="recipient", type="string", example="+1234567890"),
     *             @OA\Property(property="message", type="string", example="Your OTP is 123456"),
     *             @OA\Property(property="sender", type="string", example="+1098765432"),
     *             @OA\Property(property="vendor", type="string", example="Twilio"),
     *             @OA\Property(property="identity", type="string", example="Identity"),
     * 
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
     *     )
     * )
     */
    public function sendSms(Request $request)
    {
        $request->validate([
            'recipient' => 'required|string',
            'message' => 'required|string',
            'sender' => 'required|string',
            'vendor' => 'required|string',
        ]);

        $user = Auth::user();

        // Retrieve the API token based on the vendor
        $apiToken = SmsApiToken::where('vendor', $request->vendor)->first();

        if (!$apiToken) {
            return response()->json(['error' => 'No API token found for this vendor'], 400);
        }

        // Create SMS log entry
        $smsLog = SmsLog::create([
            'user_id' => $user->id,
            'identity' => $request->identity,
            'vendor' => $request->vendor,
            'sender' => $request->sender,
            'recipient' => $request->recipient,
            'message' => $request->message,
            'status' => 'pending',
            'token' => $apiToken->token,
            'sms_api_token_id' => $apiToken->token,
        ]);
        dd($smsLog);

        // Dispatch SMS job to the queue
        SendSmsJob::dispatch($smsLog);

        return response()->json(['message' => 'SMS is being processed'], 200);
    }
}
