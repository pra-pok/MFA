<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsApiToken extends Model
{
    protected $table = "sms_api_tokens";

    protected $fillable = [
      'vendor', 'identity', 'token'
    ];
}
