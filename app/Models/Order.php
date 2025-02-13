<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cart_details',
        'total_price',
        'used_coins',
        'coin_earned',
        'payment_slip',
        'status',
    ];

    protected $casts = [
        'cart_details' => 'array', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
