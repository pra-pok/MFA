<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationNewEvent extends Model
{
    use HasFactory;
    protected $fillable = [
        'organization_id',
        'new_event_id',
    ];

    protected $table = 'organization_new_events';
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }
    public function newEvent()
    {
        return $this->belongsTo(NewEvent::class, 'new_event_id', 'id');
    }
}
