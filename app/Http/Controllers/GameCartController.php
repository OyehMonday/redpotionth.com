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
    
        if (!isset($cart[$game->id])) {
            $cart[$game->id] = [
                'game_name' => $game->title,
                'player_id' => '',
                'uid_detail' => $game->uid_detail,
                'packages' => []
            ];
        }
    
        $uniqueId = uniqid($package->id . '_', true);
    
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
    
        return redirect()->route('game.cart.view')->with('success', '');
    }
    
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        return view('cart', compact('cart'));
    }

    public function updateCart(Request $request)
    {
        $cart = session()->get('cart', []);
    
        foreach ($request->player_ids as $game_id => $packages) {
            if (isset($cart[$game_id])) {
                foreach ($packages as $package_id => $player_id) {
                    if (isset($cart[$game_id]['packages'][$package_id])) {
                        $cart[$game_id]['packages'][$package_id]['player_id'] = $player_id;
                    }
                }
            }
        }
    
        session()->put('cart', $cart);
    
        return redirect()->route('game.checkout')->with('success', '');
    }    

    public function removeFromCart(Request $request)
    {
        $cart = session()->get('cart', []);
    
        if (isset($cart[$request->game_id]['packages'][$request->package_id])) {
            unset($cart[$request->game_id]['packages'][$request->package_id]);
    
            if (empty($cart[$request->game_id]['packages'])) {
                unset($cart[$request->game_id]);
            }
    
            if (empty($cart)) {
                session()->forget('cart');
            } else {
                session()->put('cart', $cart);
            }
        }
    
        session()->save();
    
        return redirect()->route('game.cart.view')->with('success', '');
    }        
    
    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->route('game.cart.view')->with('success', 'Cart has been cleared.');
    }

    public function checkout()
    {
        if (!Session::has('user')) {
            session()->put('url.intended', route('game.checkout'));
    
            return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบก่อนทำการชำระเงิน');
        }
    
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('game.cart.view')->with('error', 'ตะกร้าสินค้าของคุณว่างเปล่า');
        }
    
        return view('checkout', compact('cart'));
    }
    
      
}
