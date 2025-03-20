<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organizationemailconfig extends Model
{
    protected $table="organization_emailconfigs";

    public $fillable = ['organization_signup_id','mail_driver','mail_host','mail_port','mail_username','mail_password','mail_encryption','mail_from_address','mail_from_name','created_at','updated_at','created_by','updated_by'];


    public function organizationsignup(){
        return $this->belongsTo(OrganizationSignup::class,'organization_signup_id','id');
    }
    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
}
