<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SmsLog;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $smsLog;
    /**
     * Create a new job instance.
     */
    public function __construct(SmsLog $smsLog)
    {
        $this->smsLog = $smsLog;
    }


    public function handle()
    {
        // Simulate sending SMS (Use API like Twilio, Nexmo, etc.)
        $response = $this->sendSms(
            $this->smsLog->sender,
            $this->smsLog->recipient,
            $this->smsLog->message
        );

        // Update status
        $this->smsLog->update(['status' => $response ? 'sent' : 'failed']);
    }

    private function sendSms($from, $to, $message)
    {
         return true;
    }

}