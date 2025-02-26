<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GamePackage;

class AllGameController extends Controller
{
    public function index()
    {
        $carouselGames = Game::orderBy('sort_order', 'asc')->take(5)->get();
        $games = Game::orderBy('sort_order', 'asc')->paginate(100); 
        return view('games', compact('carouselGames', 'games'));
    }
    
    public function search(Request $request)
    {
        $query = $request->input('query');
    
        $gamesFromTitle = Game::where('title', 'LIKE', "%{$query}%")
                              ->orWhere('description', 'LIKE', "%{$query}%");
    
        $gameIdsFromPackages = GamePackage::where('name', 'LIKE', "%{$query}%")
                                          ->orWhere('detail', 'LIKE', "%{$query}%")
                                          ->pluck('game_id'); 
    
        $games = Game::whereIn('id', $gameIdsFromPackages)
                     ->union($gamesFromTitle)
                     ->orderBy('sort_order', 'asc')
                     ->get();
    
        return view('search', compact('games', 'query'));
    }
    
    
}
