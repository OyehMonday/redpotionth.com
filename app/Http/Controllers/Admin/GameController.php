<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with('category')->orderBy('sort_order', 'asc')->get(); 
        $categories = GameCategory::all(); 
    
        return view('admin.games.index', compact('games', 'categories'));
    }
    
        // public function index()
        // {
        //     $games = Game::with('category')->get();
        //     $categories = GameCategory::all(); // Fetch all categories
        
        //     return view('admin.games.index', compact('games', 'categories')); // Include $categories
        // }
    

    public function create()
    {
        $categories = GameCategory::all();
        return view('admin.games.create', compact('categories'));
    }
    
    public function sort(Request $request)
    {
        foreach ($request->order as $game) {
            Game::where('id', $game['id'])->update(['sort_order' => $game['sort_order']]);
        }
    
        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:games,title|max:255',
            'game_category_id' => 'required|exists:game_categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'full_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'uid_detail' => 'nullable|string|max:255',
            'uid_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
    
        $coverPath = null;
        $fullCoverPath = null;
        $uidImagePath = null;
    
        if ($request->hasFile('cover_image')) {
            $coverFile = $request->file('cover_image');
            $coverPath = $coverFile->store('game_covers', 'public');
        }
    
        if ($request->hasFile('full_cover_image')) {
            $fullCoverFile = $request->file('full_cover_image');
            $fullCoverPath = $fullCoverFile->store('game_full_covers', 'public');
        }
    
        if ($request->hasFile('uid_image')) {
            $uidImageFile = $request->file('uid_image');
            $uidImagePath = $uidImageFile->store('uidimages', 'public');
        }     
    
        Game::create([
            'title' => $request->title,
            'game_category_id' => $request->game_category_id,
            'uid_image' => $uidImagePath,
            'uid_detail' => $request->uid_detail,
            'cover_image' => $coverPath,
            'full_cover_image' => $fullCoverPath,
        ]);
    
        return redirect()->route('games.index')->with('success', 'Game added successfully.');
    }
    

    public function edit(Game $game)
    {
        $categories = GameCategory::all();
        return view('admin.games.edit', compact('game', 'categories'));
    }

    public function update(Request $request, Game $game)
    {
        $request->validate([
            'title' => 'required|max:255|unique:games,title,' . $game->id,
            'game_category_id' => 'required|exists:game_categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'full_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'uid_detail' => 'nullable|string|max:255',
            'uid_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
    
        if ($request->file('cover_image')) {
            if ($game->cover_image) {
                Storage::disk('public')->delete($game->cover_image);
            }
            $game->cover_image = $request->file('cover_image')->store('game_covers', 'public');
        }
    
        if ($request->file('full_cover_image')) {
            if ($game->full_cover_image) {
                Storage::disk('public')->delete($game->full_cover_image);
            }
            $game->full_cover_image = $request->file('full_cover_image')->store('game_full_covers', 'public');
        }
    
        if ($request->file('uid_image')) {
            if ($game->uid_image) {
                Storage::disk('public')->delete($game->uid_image);
            }
            $game->uid_image = $request->file('uid_image')->store('uidimages', 'public');
        }
    
        $game->update([
            'title' => $request->title,
            'game_category_id' => $request->game_category_id,
            'uid_image' => $game->uid_image,
            'uid_detail' => $request->uid_detail,
            'cover_image' => $game->cover_image,
            'full_cover_image' => $game->full_cover_image,
        ]);
    
        return redirect()->route('games.index')->with('success', 'Game updated successfully.');
    }    

public function destroy(Game $game)
{
    if ($game->cover_image) {
        Storage::disk('public')->delete($game->cover_image);
    }

    if ($game->full_cover_image) {
        Storage::disk('public')->delete($game->full_cover_image);
    }

    if ($game->uid_image) {
        Storage::disk('public')->delete($game->uid_image);
    }

    $game->delete();

    return redirect()->route('games.index')->with('success', 'Game deleted successfully.');
}

}
