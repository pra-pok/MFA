<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUp extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'student_id',
        'date',
        'via',
        'note',
        'status_id',
        'is_current_status',
        'next_date_time',
    ];

    protected $table = 'follow_up';

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function status()
    {
        return $this->belongsTo(CounsellingStatus::class);
    }
}
