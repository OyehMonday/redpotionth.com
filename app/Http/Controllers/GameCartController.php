<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GamePackage;
use App\Models\Game;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log; 

class GameCartController extends Controller
{
    
    public function addToCart(Request $request)
    {
        $package = GamePackage::findOrFail($request->package_id);
        $game = Game::findOrFail($package->game_id);
    
        if (!Session::has('user')) {
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
    
        $user = Session::get('user');
        $existingOrder = Order::where('user_id', $user->id)->where('status', '1')->first();
    
        if (!$existingOrder) {
            $cartData = [
                $game->id => [
                    'game_name' => $game->title,
                    'player_id' => '',
                    'uid_detail' => $game->uid_detail,
                    'packages' => []
                ]
            ];
    
            $uniqueId = uniqid($package->id . '_', true);
    
            $cartData[$game->id]['packages'][$uniqueId] = [
                'unique_id' => $uniqueId,
                'package_id' => $package->id,
                'name' => $package->name,
                'detail' => $package->detail,
                'price' => $package->selling_price,
                'full_price' => $package->full_price,
                'cover_image' => $package->cover_image
            ];
    
            $order = Order::create([
                'user_id' => $user->id,
                'cart_details' => json_encode($cartData),
                'total_price' => $package->selling_price,
                'status' => '1', 
            ]);
    
            session()->put('order_id', $order->id);
        } else {
            $cartData = json_decode($existingOrder->cart_details, true);
    
            if (!isset($cartData[$game->id])) {
                $cartData[$game->id] = [
                    'game_name' => $game->title,
                    'player_id' => '',
                    'uid_detail' => $game->uid_detail,
                    'packages' => []
                ];
            }
    
            $uniqueId = uniqid($package->id . '_', true);
    
            $cartData[$game->id]['packages'][$uniqueId] = [
                'unique_id' => $uniqueId,
                'package_id' => $package->id,
                'name' => $package->name,
                'detail' => $package->detail,
                'price' => $package->selling_price,
                'full_price' => $package->full_price,
                'cover_image' => $package->cover_image
            ];
    
            $existingOrder->update([
                'cart_details' => json_encode($cartData),
                'total_price' => collect($cartData)->pluck('packages')->flatten(1)->sum('price'),
            ]);
        }
    
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
        $game_id = $request->game_id;
        $package_id = $request->package_id;
    
        if (!Session::has('user')) {
            $cart = session()->get('cart', []);
            if (isset($cart[$game_id]['packages'][$package_id])) {
                unset($cart[$game_id]['packages'][$package_id]);
    
                if (empty($cart[$game_id]['packages'])) {
                    unset($cart[$game_id]);
                }
    
                session()->put('cart', $cart);
            }
    
            return redirect()->route('game.cart.view')->with('success', 'ลบสินค้าออกจากตะกร้าเรียบร้อยแล้ว');
        }
    
        $user = Session::get('user');
        $cart = session()->get('cart', []);
    
        if (isset($cart[$game_id]['packages'][$package_id])) {
            unset($cart[$game_id]['packages'][$package_id]);
    
            if (empty($cart[$game_id]['packages'])) {
                unset($cart[$game_id]);
            }
    
            session()->put('cart', $cart);
        }
    
        $existingOrder = Order::where('user_id', $user->id)
                              ->where('status', '1')
                              ->first();
    
        if ($existingOrder) {
            $existingCart = json_decode($existingOrder->cart_details, true);
    
            if (isset($existingCart[$game_id]['packages'][$package_id])) {
                unset($existingCart[$game_id]['packages'][$package_id]);
    
                if (empty($existingCart[$game_id]['packages'])) {
                    unset($existingCart[$game_id]);
                }
    
                $existingOrder->update([
                    'cart_details' => json_encode($existingCart),
                    'total_price' => collect($existingCart)->pluck('packages')->flatten(1)->sum('price'),
                ]);
            }
        }
    
        return redirect()->route('game.cart.view')->with('success', 'ลบสินค้าออกจากตะกร้าเรียบร้อยแล้ว');
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
    
        if (!empty($cart)) {
            Order::where('user_id', Session::get('user')->id)
                ->where('status', '1') 
                ->delete();
        } else {
            $this->loadCartFromDatabase();
            $cart = session()->get('cart', []);
        }
    
        if (empty($cart)) {
            return redirect()->route('game.cart.view')->with('error', 'ตะกร้าสินค้าของคุณว่างเปล่า');
        }
    
        $updatedCart = [];
        foreach ($cart as $game_id => $game) {
            foreach ($game['packages'] as $package_id => $package) {
                $gamePackage = GamePackage::find($package_id);
                if ($gamePackage) {
                    $updatedCart[$game_id]['game_name'] = $game['game_name'];
                    $updatedCart[$game_id]['packages'][$package_id] = [
                        'name' => $gamePackage->name,
                        'full_price' => $gamePackage->full_price ?? null, 
                        'price' => $gamePackage->selling_price,
                        'player_id' => $package['player_id'],
                    ];
                }
            }
        }
    
        session()->put('cart', $updatedCart);
    
        $totalPrice = collect($updatedCart)->pluck('packages')->flatten(1)->sum('price');
    
        $order = Order::create([
            'user_id' => Session::get('user')->id,
            'cart_details' => json_encode($updatedCart),
            'total_price' => $totalPrice,
            'status' => '1', 
        ]);
        session()->put('order_id', $order->id);
    
        return redirect()->route('game.checkout.view', ['order_id' => session('order_id')]);
    }

    public function loadCartFromDatabase()
    {
        if (!Session::has('user') || Session::has('cart')) {
            return; 
        }
    
        $user = Session::get('user');
        $existingOrder = Order::where('user_id', $user->id)
                              ->where('status', '1') 
                              ->latest()->first();
    
        if ($existingOrder) {
            session()->put('cart', json_decode($existingOrder->cart_details, true));
            session()->put('order_id', $existingOrder->id);
        }
    }
    
    
    public function showCheckout($order_id)
    {
        if (!Session::has('user')) {
            session()->put('url.intended', route('game.checkout.view', ['order_id' => $order_id]));
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
    
    public function confirmPayment(Request $request, $order_id)
    {
        if (!Session::has('user')) {
            return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบก่อนแนบสลิปการชำระเงิน');
        }
    
        $request->validate([
            'payment_slip' => 'required|mimes:jpeg,png,pdf|max:2048',
        ]);
    
        $order = Order::where('id', $order_id)->where('user_id', Session::get('user')->id)->first();
    
        if (!$order) {
            return redirect()->route('game.cart.view')->with('error', 'ไม่พบคำสั่งซื้อนี้');
        }
    
        $filePath = $request->file('payment_slip')->store('payments', 'public');
    
        $order->update([
            'payment_slip' => $filePath,
            'status' => '2',
        ]);
    
        return redirect()->route('dashboard')->with('success', 'ได้รับคำสั่งซื้อแล้ว.');
    }
    
    
}
