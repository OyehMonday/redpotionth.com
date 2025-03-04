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
        'refslip',
        'refqr',
        'in_process_by',
        'status',
        'in_process_by',
    ];

    protected $casts = [
        'cart_details' => 'array', 
        'payment_approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }       
    
    public function inProcessBy()
    {
        return $this->belongsTo(Admin::class, 'in_process_by');
    }

}
