<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Team extends Model
{
    use HasRoles;

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // public static function boot()
    // {
    //     parent::boot();

    //     self::created(function ($model) {
    //        $session_team_id = getPermissionsTeamId();
    //        setPermissionsTeamId($model);
    //        User::where(['team_id' => $session_team_id])->assignRole('Super Admin');
    //        // restore session team_id to package instance using temporary value stored above
    //        setPermissionsTeamId($session_team_id);
    //     });
    // }
}
