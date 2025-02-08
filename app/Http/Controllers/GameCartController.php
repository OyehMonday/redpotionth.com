<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GamePackage;
use App\Models\Game;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\User;

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

    public function checkout(Request $request)
    {
        if (!Session::has('user')) {
            session()->put('url.intended', route('game.checkout'));
            return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบก่อนทำการชำระเงิน');
        }
    
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('game.cart.view')->with('error', 'ตะกร้าสินค้าของคุณว่างเปล่า');
        }
    
        $totalPrice = collect($cart)->pluck('packages')->flatten(1)->sum('price');
    
        $existingOrder = Order::where('user_id', Session::get('user')->id)
                              ->where('status', '1')
                              ->first();
    
        if ($existingOrder) {
            $existingOrder->update([
                'cart_details' => json_encode($cart),
                'total_price' => $totalPrice,
            ]);
            session()->put('order_id', $existingOrder->id); 
        } else {
            $order = Order::create([
                'user_id' => Session::get('user')->id,
                'cart_details' => json_encode($cart),
                'total_price' => $totalPrice,
                'status' => '1', 
            ]);
            session()->put('order_id', $order->id);
        }
    
        return redirect()->route('game.checkout.view', ['order_id' => session('order_id')]);
    }      
    
    public function showCheckout($order_id)
    {
        if (!Session::has('user')) {
            return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบก่อนทำการชำระเงิน');
        }
    
        $user = Session::get('user');
        $order = Order::where('id', $order_id)->where('user_id', $user->id)->first();
    
        if (!$order) {
            return redirect()->route('game.cart.view')->with('error', 'คุณไม่มีสิทธิ์เข้าถึงคำสั่งซื้อนี้');
        }
    
        if (request()->has('confirm_checkout')) {
            session()->forget(['cart', 'order_id']);
        }
    
        return view('checkout', compact('order'));
    }         
    
}
