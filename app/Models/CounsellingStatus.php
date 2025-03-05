<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CounsellingStatus extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'color',
        'note',
        'created_by',
        'updated_by',
    ];
    protected $table = 'counselling_status';
    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
}
