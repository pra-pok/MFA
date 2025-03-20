<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emailtracking extends Model
{
    protected $fillable = ['organization_signup_id','email','subject','message','status','batch'];

    public function organizationsignup(){
        return $this->belongsTo(OrganizationSignup::class,'organization_signup_id','id');

    }
}
