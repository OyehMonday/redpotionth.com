<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class HomeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth')->except(['index']); // Allow unauthenticated access to index()
    // }

    public function index()
    {
        $games = Game::orderBy('sort_order', 'asc')->take(12)->get(); 
        return view('landing', compact('games'));
    }
}
