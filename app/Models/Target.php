<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Target extends Model
{
    use HasFactory;
    protected $fillable = [
        'target_group_id',
        'min_target',
        'max_target',
        'amount_percentage',
        'type',
        'per_student'
    ];
    protected $table = 'targets';

    public function targetGroup()
    {
        return $this->belongsTo(TargetGroup::class, 'target_group_id');
    }
}
