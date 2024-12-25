<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'appointment_date',
        'status',
    ];

    public function patient(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function doctor(){
        return $this->belongsTo(Doctor::class);
    }
}
