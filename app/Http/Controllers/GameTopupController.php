<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GamePackage; // Ensure this model is imported

class GameTopupController extends Controller
{
    public function show($id)
    {
        $game = Game::findOrFail($id);
        $packages = GamePackage::where('game_id', $id)->orderBy('sort_order', 'asc')->get();
    
        return view('topup', compact('game', 'packages'));
    }    
}
