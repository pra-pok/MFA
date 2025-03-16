<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $table = "sms_logs";
    protected $fillable = [
      'vendor','identity','token','organization_id','sender','sms_api_token_id'
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
