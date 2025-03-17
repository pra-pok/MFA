<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $table = "sms_logs";
    protected $fillable = [
      'vendor','recipients','message','organization_id','organization_name','sender_phone_number','sender','sms_api_token_id','status','response'
    ];
    
    public function smsApiToken()
    {
        return $this->belongsTo(SmsApiToken::class);
    }

    public function user()
    {
        return $this->belongsTo(OrganizationSignup::class);
    }
}
