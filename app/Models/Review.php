<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'rating',
        'organization_id',
    ];

    protected $table = 'reviews';

}
