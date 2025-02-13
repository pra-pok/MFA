<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniversityCatalog extends Model
{
    use HasFactory;
    protected $fillable = [
        'university_id',
        'catalog_id',
    ];

    protected $table = 'university_catalogs';
    public function catalog(){
        return $this->belongsTo(Catalog::class,'catalog_id','id');
    }
    public function university(){
        return $this->belongsTo(University::class,'university_id','id');
    }
}
