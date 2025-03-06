<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsAppMessage extends Model
{  
    use HasFactory;

    protected $table = 'whatsapp_messages'; 
    protected $fillable = [
        'recipient_phone',
        'recipient_group',
        'media_path',
        'message',
        'status',
    ];
    
    public function createds(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class,'updated_by','id');
    }
}
