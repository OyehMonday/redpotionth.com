<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable; 

class Admin extends Model
{
    use HasFactory, Notifiable;  // Add Notifiable trait here

    // Add the 'name', 'email', 'password', etc., to the fillable property
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verification_token',
        'is_verified',  // Optional: add this if you want to mass-assign 'is_verified'
    ];

    // Optionally, you can also hide certain fields from the array representation
    protected $hidden = [
        'password', 'remember_token',
    ];
}
