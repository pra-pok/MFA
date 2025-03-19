<?php

namespace App\Jobs;

use App\Models\SmsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipients;
    protected $message;
    protected $authToken;
    protected $smsLogId;

    /**
     * Create a new job instance.
     */
    public function __construct($recipients, $message, $authToken, $smsLogId)
    {
        $this->recipients = $recipients;
        $this->message = $message;
        $this->authToken = $authToken;
        $this->smsLogId = $smsLogId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $postData = [
                'auth_token' => $this->authToken,
                'to' => $this->recipients,
                'text' => $this->message,
            ];

            $response = Http::post('https://sms.aakashsms.com/sms/v3/send', $postData);

            // Fetch the SMS log entry and update the status
            $smsLog = SmsLog::find($this->smsLogId);
            if ($smsLog) {
                $smsLog->update([
                    'status' => $response->successful() ? 'sent' : 'failed',
                    'response' => $response->body(),
                ]);
            }

            Log::info('SMS Sent Successfully:', ['response' => $response->json()]);

        } catch (\Exception $e) {
            Log::error('SMS Sending Failed:', ['error' => $e->getMessage()]);

            // Update SMS log as failed
            $smsLog = SmsLog::find($this->smsLogId);
            if ($smsLog) {
                $smsLog->update([
                    'status' => 'failed',
                    'response' => $e->getMessage(),
                ]);
            }
        }
    }
}
