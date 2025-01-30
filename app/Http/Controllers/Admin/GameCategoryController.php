<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameCategory;
use Illuminate\Http\Request;

class GameCategoryController extends Controller
{
    public function index()
    {
        $categories = GameCategory::all();
        return view('admin.game-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.game-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:game_categories,name',
        ]);
    
        GameCategory::create([
            'name' => $request->name,
        ]);
    
        return redirect()->route('games.index')->with('success', 'Category added successfully.');
    }

    public function edit(GameCategory $gameCategory)
    {
        return view('admin.game-categories.edit', compact('gameCategory'));
    }

    public function update(Request $request, GameCategory $gameCategory)
    {
        $request->validate([
            'name' => 'required|unique:game_categories,name,' . $gameCategory->id
        ]);

        $gameCategory->update(['name' => $request->name]);

        return redirect()->route('games.index')->with('success', 'Category updated.');
    }

    public function destroy(GameCategory $gameCategory)
    {
        if ($gameCategory->games()->count()) {
            return redirect()->route('games.index')->with('error', 'Cannot delete category with assigned games.');
        }

        $gameCategory->delete();
        return redirect()->route('games.index')->with('success', 'Category deleted.');
    }
}
