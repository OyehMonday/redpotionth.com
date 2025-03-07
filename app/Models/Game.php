<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'game_category_id', 'cover_image', 'uid_detail', 'uid_image', 'full_cover_image', 'sort_order', 'description'];

    public function category()
    {
        return $this->belongsTo(GameCategory::class, 'game_category_id');
    }
    
    public function packages()
    {
        return $this->hasMany(GamePackage::class);
    }
    
}
