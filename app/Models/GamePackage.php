<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamePackage extends Model
{
    use HasFactory;

    protected $fillable = ['game_id', 'name', 'full_price', 'selling_price', 'cover_image', 'detail', 'sort_order'];

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'id'); 
    }
    
}
