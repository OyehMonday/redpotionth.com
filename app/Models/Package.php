<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'game_packages'; 

    protected $fillable = [
        'game_id', 'name', 'full_price', 'selling_price',
        'detail', 'highlight', 'cover_image'
    ];
    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

}
