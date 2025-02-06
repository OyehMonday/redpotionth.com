<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GamePackage;
use App\Models\Game;
use Illuminate\Support\Facades\Session;

class GameCartController extends Controller
{
    public function addToCart(Request $request)
    {
        $package = GamePackage::findOrFail($request->package_id);
        $game = Game::findOrFail($package->game_id);
    
        $cart = session()->get('cart', []);
    
        // Ensure game entry exists in the cart
        if (!isset($cart[$game->id])) {
            $cart[$game->id] = [
                'game_name' => $game->title,
                'player_id' => '',
                'uid_detail' => $game->uid_detail,
                'packages' => []
            ];
        }
    
        // Generate a unique ID using timestamp
        $uniqueId = uniqid($package->id . '_', true);
    
        // Add a new entry every time "Add to Cart" is clicked
        $cart[$game->id]['packages'][$uniqueId] = [
            'unique_id' => $uniqueId, 
            'package_id' => $package->id,
            'name' => $package->name,
            'detail' => $package->detail,
            'price' => $package->selling_price,
            'full_price' => $package->full_price,
            'cover_image' => $package->cover_image
        ];
    
        session()->put('cart', $cart);
    
        return redirect()->route('game.cart.view')->with('success', 'Item added to cart!');
    }
    
    /**
     * Display the cart.
     */
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        return view('cart', compact('cart'));
    }

    /**
     * Update Player ID for each game.
     */
    public function updateCart(Request $request)
    {
        $cart = session()->get('cart', []);

        foreach ($request->player_ids as $game_id => $player_id) {
            if (isset($cart[$game_id])) {
                $cart[$game_id]['player_id'] = $player_id;
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('game.cart.view')->with('success', 'Cart updated successfully!');
    }

    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);
    
        if (isset($cart[$request->game_id]['packages'][$request->package_id])) {
            unset($cart[$request->game_id]['packages'][$request->package_id]);
    
            // If no more packages exist under this game, remove the game from the cart
            if (empty($cart[$request->game_id]['packages'])) {
                unset($cart[$request->game_id]);
            }
    
            // If the cart is now empty, remove it completely
            if (empty($cart)) {
                session()->forget('cart');
            } else {
                session()->put('cart', $cart);
            }
        }
    
        session()->save();
    
        return redirect()->route('game.cart.view')->with('success', 'นำสินค้าออกจากตะกร้าแล้ว');
    }        
    
    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->route('game.cart.view')->with('success', 'Cart has been cleared.');
    }
}
