<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'effective_from',
        'valid_till',
        'is_current',
        'created_by',
        'updated_by',
    ];

    protected $table = 'academic_years';

    public function createds(){
        return $this->belongsTo(OrganizationSignup::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(OrganizationSignup::class,'updated_by','id');
    }

}
