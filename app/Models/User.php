<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = ['username', 'email', 'password', 'email_verified_at', 'verification_token'];
    protected $hidden = [
        'password', 
        'remember_token',
        'verification_token',
    ];
}
