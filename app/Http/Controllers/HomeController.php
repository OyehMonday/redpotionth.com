<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Package;

class HomeController extends Controller
{
    public function index()
    {
        $games = Game::orderBy('sort_order', 'asc')->take(12)->get(); 

        $highlightedPackages = Package::with('game')
            ->whereNotNull('highlight')
            ->where('highlight', '>', 0)
            ->orderBy('highlight', 'asc')
            ->limit(10)
            ->get();

        return view('landing', compact('games', 'highlightedPackages'));
    }
}
