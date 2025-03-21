<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmsLog;
use App\Models\SmsApiToken;
use App\Jobs\SendSmsJob;
use Maatwebsite\Excel\Facades\Excel;
use Auth;

class SmsController extends Controller
{
    protected $panel = 'History';
    protected $base_route = 'admin.whatsapp-messages';
    protected $view_path = 'admin.components.whatsapp_messages';
    protected $model;
    protected $table;
    protected $folder = 'whatsapp_messages';
    protected $smsService;

    public function sendSms(Request $request)
    {  
        $request->validate([
            'vendor' => 'required|string|exists:sms_api_tokens,vendor',
            'message' => 'required|string',
            'contacts_file' => 'required|file|mimes:csv,xlsx', // Validate file extension
        ]);
    
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        $apiToken = SmsApiToken::where('vendor', $request->vendor)->first();
        if (!$apiToken) {
            return response()->json(['error' => 'Invalid vendor'], 400);
        }
    
        // Process the file if uploaded
        if ($request->hasFile('contacts_file')) {
            $path = $request->file('contacts_file')->getRealPath();
            $extension = $request->file('contacts_file')->getClientOriginalExtension();
    
            // Handle the file based on its extension
            if ($extension == 'csv') {
                $contacts = Excel::toArray([], $path); // Automatically detects CSV
            } elseif ($extension == 'xlsx') {
                $contacts = Excel::toArray([], $path); // Automatically detects XLSX
            } else {
                return response()->json(['error' => 'Invalid file type. Only CSV or XLSX files are allowed.'], 400);
            }
    
            // Assuming 'phone_number' is the correct column in your file
            $recipients = array_column($contacts[0], 'phone_number'); 
    
            // Log or process recipients as needed...
        }
    
        // Send SMS to all recipients
        foreach ($recipients as $recipient) {
            $smsLog = SmsLog::create([
                'vendor' => $apiToken->vendor,
                'recipients' => $recipient,
                'message' => $request->message,
                'sender_phone_number' => $user->phone,
                'status' => 'pending',
                'sms_api_token_id' => $apiToken->id,
                'organization_id' => $user->id,
                'organization_name' => $user->username,
                'response' => null,
            ]);
    
            dispatch(new SendSmsJob(
                $recipient,
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
