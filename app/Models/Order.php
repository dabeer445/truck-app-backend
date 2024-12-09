<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'pickup_location',
        'delivery_location',
        'cargo_details',
        'pickup_time',
        'delivery_time',
        'status',
        'notes'
    ];

    const STATUSES = [
        'pending',
        'in_progress',
        'completed',
        'cancelled'
    ];
    
    protected $casts = [
        'cargo_details' => 'array',
        'pickup_time' => 'datetime',
        'delivery_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
