<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'role',
        'parent_id',
        'name',
        'url',
        'rank',
        'icon',
        'permission_key',
        'is_active',
        'is_view_menu',
        'created_by',
        'updated_by',
    ];
    protected $table = 'menus';
    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function parent(){
        return $this->belongsTo(Menu::class,'parent_id', 'id');
    }
    public function children(){
        return $this->hasMany(Menu::class,'parent_id','id');
    }
    public function directParent()
    {
        return $this->belongsTo(Menu::class, 'parent_id')->with('directParent');
    }

    public function grandParent()
    {
        return $this->belongsTo(Menu::class, 'parent_id')->with('directParent.grandParent');
    }
    public function greatGrandParent()
    {
        return $this->belongsTo(Menu::class, 'parent_id')->with('directParent.grandParent.greatGrandParent');
    }
    public function getAncestor($level)
    {
        $parent = $this->parent;
        for ($i = 1; $i < $level; $i++) {
            if (!$parent) break;
            $parent = $parent->parent;
        }
        return $parent;
    }
}
