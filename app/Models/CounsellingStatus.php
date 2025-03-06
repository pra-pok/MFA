<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CounsellingStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'color',
        'note',
    ];
    protected $table = 'counselling_status';
}
