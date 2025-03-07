<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CounselorReferrer extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'role',
        'created_by',
        'updated_by',
    ];
    protected $table = 'counselor_referrers';
    public function createds(){
        return $this->belongsTo(OrganizationSignup::class,'created_by','id');
    }
    public function updatedBy(){
        return $this->belongsTo(OrganizationSignup::class,'updated_by','id');
    }
}
