<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GamePackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GamePackageController extends Controller
{
    public function index(Game $game)
    {
        $packages = $game->packages()->orderBy('sort_order', 'asc')->get();
    
        return view('admin.game-packages.index', compact('game', 'packages'));
    }     

    public function sort(Request $request, Game $game)
    {
        foreach ($request->order as $package) {
            GamePackage::where('id', $package['id'])->update(['sort_order' => $package['sort_order']]);
        }
    
        return response()->json(['success' => true]);
    }       

    public function create(Game $game)
    {
        return view('admin.game-packages.create', compact('game'));
    }    

    public function store(Request $request, Game $game)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'full_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|lte:full_price', 
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'detail' => 'nullable|string',
        ]);
    
        $coverImagePath = null;

        // if ($request->hasFile('cover_image')) {
        //     $coverImageFile = $request->file('cover_image');
        //     $coverImageFilename = time() . '_' . $coverImageFile->getClientOriginalName();
        //     $coverImageFile->move(public_path('package_covers'), $coverImageFilename);
        //     $coverImagePath = 'package_covers/' . $coverImageFilename;
        // }          
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('package_covers', 'public');
        }
    
        $lastSortOrder = $game->packages()->max('sort_order') ?? 0;
    
        $game->packages()->create([
            'name' => $request->name,
            'full_price' => $request->full_price,
            'selling_price' => $request->selling_price,
            'cover_image' => $coverImagePath,
            'detail' => $request->detail,
            'sort_order' => $lastSortOrder + 1,
        ]);
    
        return redirect()->route('game-packages.index', $game)->with('success', 'Package added successfully.');
    }     
    
    public function edit(Game $game, GamePackage $package)
    {
        return view('admin.game-packages.edit', compact('game', 'package'));
    }    

    public function update(Request $request, Game $game, GamePackage $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'full_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|lte:full_price',
            'detail' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        if ($request->hasFile('cover_image')) {
            if ($package->cover_image) {
                Storage::disk('public')->delete($package->cover_image);
            }
            $package->cover_image = $request->file('cover_image')->store('package_covers', 'public');
        }
    
        $package->update([
            'name' => $request->name,
            'full_price' => $request->full_price,
            'selling_price' => $request->selling_price,
            'detail' => $request->detail,
            'cover_image' => $package->cover_image,
        ]);
    
        return redirect()->route('game-packages.index', $game)->with('success', 'Package updated successfully.');
    }
     

    public function destroy(Game $game, GamePackage $package)
    {
        $package->delete();
        return redirect()->route('game-packages.index', $game)->with('success', 'Package deleted successfully.');
    }
}
