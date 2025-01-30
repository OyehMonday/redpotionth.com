<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;

class GameTopupController extends Controller
{
    public function show($id)
    {
        $game = Game::findOrFail($id);
        return view('topup', compact('game'));
    }
}
