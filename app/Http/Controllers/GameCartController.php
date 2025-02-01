<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GamePackage;
use App\Models\Game;
use Illuminate\Support\Facades\Session;

class GameCartController extends Controller
{
    /**
     * Add a package to the cart.
     */
    public function addToCart(Request $request)
    {
        $package = GamePackage::findOrFail($request->package_id);
        $game = Game::findOrFail($package->game_id);

        $cart = session()->get('cart', []);

        // If the game doesn't exist in the cart, initialize it
        if (!isset($cart[$game->id])) {
            $cart[$game->id] = [
                'game_name' => $game->title,
                'player_id' => '', // User will input this later
                'packages' => []
            ];
        }

        // Add or update package under the game
        if (isset($cart[$game->id]['packages'][$package->id])) {
            $cart[$game->id]['packages'][$package->id]['quantity']++;
        } else {
            $cart[$game->id]['packages'][$package->id] = [
                'name' => $package->name,
                'detail' => $package->detail,
                'price' => $package->selling_price,
                'full_price' => $package->full_price,
                'quantity' => 1,
                'cover_image' => $package->cover_image
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('game.cart.view')->with('success', 'Package added to cart!');
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

    /**
     * Remove a package from the cart.
     */
    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$request->game_id]['packages'][$request->package_id])) {
            unset($cart[$request->game_id]['packages'][$request->package_id]);

            // If no more packages exist under this game, remove the game from the cart
            if (empty($cart[$request->game_id]['packages'])) {
                unset($cart[$request->game_id]);
            }

            session()->put('cart', $cart);
        }

        return redirect()->route('game.cart.view')->with('success', 'Item removed from cart.');
    }

    /**
     * Clear the cart completely.
     */
    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->route('game.cart.view')->with('success', 'Cart has been cleared.');
    }
}
