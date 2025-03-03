<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Package;

class AdminPackageController extends Controller
{
    public function index()
    {
        $selectedPackages = Package::with('game') 
            ->whereNotNull('highlight')
            ->where('highlight', '>', 0)
            ->orderBy('highlight', 'asc')
            ->get();
    
        $selectedIds = $selectedPackages->pluck('id');
    
        $allPackages = Package::with('game')
            ->whereNotIn('id', $selectedIds)
            ->orderBy('game_id', 'asc') 
            ->orderBy('name', 'asc') 
            ->get();
    
        return view('admin.game-packages.package', compact('selectedPackages', 'allPackages'));
    }
    
    
    public function sort(Request $request)
    {
        if (!isset($request->order) || !is_array($request->order)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid order data'], 400);
        }
    
        foreach ($request->order as $item) {
            Package::where('id', $item['id'])->update(['highlight' => $item['highlight']]);
        }
    
        return response()->json(['status' => 'success']);
    }
    
    
    public function removeHighlight(Request $request)
    {
        Package::where('id', $request->id)->update(['highlight' => null]);
    
        return response()->json(['status' => 'success']);
    }
         
    
}
