<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmsLog;
use App\Models\SmsApiToken;
use App\Jobs\SendSmsJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Logging\Log;
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
     *     path="/api/v1/sms/send",
     *     security={{"Bearer": {}}},
     *     summary="Send SMS to a recipient",
     *     description="Send SMS to a specified recipient using a vendor API token",
     *     operationId="sendSms",
     *     tags={"SMS API"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"recipients", "message", "sender", "vendor"},
     *             @OA\Property(property="recipients", type="string", example="+1234567890"),
     *             @OA\Property(property="message", type="text", example="Your OTP is 123456"),
     *             @OA\Property(property="sender_phone_number", type="string", example="+1098765432"),
     *             @OA\Property(property="vendor", type="string", example="vendor"),
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
             'recipients' => 'required|string',
             'message' => 'required|string',
             'sender_phone_number' => 'required|string',
             'vendor' => 'required|string',
         ]);
     
         \Log::info('Request Data: ', $request->all());  // Log the incoming request data
     
         $user = auth()->user();
         if (!$user) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
     
         $apiToken = SmsApiToken::where('vendor', $request->vendor)->first();
         if (!$apiToken) {
             return response()->json(['error' => 'Invalid vendor'], 400);
         }
     
         // Log data to be inserted
         \Log::info('Data to be inserted into SmsLog:', [
             'vendor' => $apiToken->vendor,
             'recipients' => $request->recipients,
             'message' => $request->message,
             'organization_id' => $user->id,
             'organization_name' => $user->username,
             'sender_phone_number' => $request->sender_phone_number,
             'status' => 'pending',
             'sms_api_token_id' => $apiToken->id,
             'response' => null,
         ]);
     
         // Insert the data
         $smsLog = SmsLog::create([
             'vendor' => $apiToken->vendor, 
             'recipients' => $request->recipients,
             'message' => $request->message,
             'organization_id' => $user->id,  
             'organization_name' => $user->username,
             'sender_phone_number' => $request->sender_phone_number,
             'status' => 'pending',  
             'sms_api_token_id' => $apiToken->id, 
             'response' => null,
         ]);
     
         // Log the inserted data
         \Log::info('SmsLog Inserted:', $smsLog->toArray());
     
         // Dispatch SMS sending job
         SendSmsJob::dispatch($smsLog);
     
         return response()->json(['message' => 'SMS is being processed'], 200);
     }}
