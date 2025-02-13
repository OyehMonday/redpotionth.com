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
        if (!Session::has('user')) {
            return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบก่อนทำการชำระเงิน');
        }
    
        $user = Session::get('user');
    
        // ✅ Check if the order has reached status 3 (payment pending or completed)
        $order = Order::where('user_id', $user->id)
                      ->where('status', '>=', 3)
                      ->first();
    
        if ($order) {
            session()->forget(['cart', 'order_id']);
    
            // ✅ Instead of redirecting immediately, return a view showing "Cart is empty"
            return view('cart', ['cart' => []])->with('error', 'ไม่มีสินค้าในตะกร้า');
        }
    
        $cart = session()->get('cart', []);
        return view('cart', compact('cart'));
    }
    

    // public function viewCart()
    // {
    //     $cart = session()->get('cart', []);
    //     return view('cart', compact('cart'));
    // }

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
        
        session()->put('use_coins', $request->input('use_coins', 0));
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
    
                if (empty($cart)) {
                    session()->forget('cart'); 
                } else {
                    session()->put('cart', $cart);
                }
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
    
                if (empty($existingCart)) {
                    $existingOrder->delete();
                    session()->forget('cart'); 
                    return redirect()->route('game.cart.view')->with('success', 'คำสั่งซื้อถูกลบเนื่องจากไม่มีสินค้าในตะกร้า');
                } else {
                    $existingOrder->update([
                        'cart_details' => json_encode($existingCart),
                        'total_price' => collect($existingCart)->pluck('packages')->flatten(1)->sum('price'),
                    ]);
                }
            }
        }
    
        return redirect()->route('game.cart.view')->with('success', 'ลบสินค้าออกจากตะกร้าเรียบร้อยแล้ว');
    }    

    // public function removeFromCart(Request $request)
    // {
    //     $game_id = $request->game_id;
    //     $package_id = $request->package_id;
    
    //     if (!Session::has('user')) {
    //         $cart = session()->get('cart', []);
    //         if (isset($cart[$game_id]['packages'][$package_id])) {
    //             unset($cart[$game_id]['packages'][$package_id]);
    
    //             if (empty($cart[$game_id]['packages'])) {
    //                 unset($cart[$game_id]);
    //             }
    
    //             session()->put('cart', $cart);
    //         }
    
    //         return redirect()->route('game.cart.view')->with('success', 'ลบสินค้าออกจากตะกร้าเรียบร้อยแล้ว');
    //     }
    
    //     $user = Session::get('user');
    //     $cart = session()->get('cart', []);
    
    //     if (isset($cart[$game_id]['packages'][$package_id])) {
    //         unset($cart[$game_id]['packages'][$package_id]);
    
    //         if (empty($cart[$game_id]['packages'])) {
    //             unset($cart[$game_id]);
    //         }
    
    //         session()->put('cart', $cart);
    //     }
    
    //     $existingOrder = Order::where('user_id', $user->id)
    //                           ->where('status', '1')
    //                           ->first();
    
    //     if ($existingOrder) {
    //         $existingCart = json_decode($existingOrder->cart_details, true);
    
    //         if (isset($existingCart[$game_id]['packages'][$package_id])) {
    //             unset($existingCart[$game_id]['packages'][$package_id]);
    
    //             if (empty($existingCart[$game_id]['packages'])) {
    //                 unset($existingCart[$game_id]);
    //             }
    
    //             $existingOrder->update([
    //                 'cart_details' => json_encode($existingCart),
    //                 'total_price' => collect($existingCart)->pluck('packages')->flatten(1)->sum('price'),
    //             ]);
    //         }
    //     }
    
    //     return redirect()->route('game.cart.view')->with('success', 'ลบสินค้าออกจากตะกร้าเรียบร้อยแล้ว');
    // }    
    
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
    
        $user = Session::get('user');

        Order::where('user_id', $user->id)
        ->where('status', '2')
        ->delete();        

        $totalPrice = collect($cart)->pluck('packages')->flatten(1)->sum('price');    
        $useCoins = session()->get('use_coins', 0);    
        $coinsAvailable = \App\Models\User::where('id', $user->id)->value('coins') ?? 0;    
        $maxDiscount = floor($totalPrice * (env('COIN_DISCOUNT_LIMIT', 50) / 100));    
        $coinsToUse = $useCoins ? min($coinsAvailable, $maxDiscount) : 0;    
        $finalAmount = max(0, $totalPrice - $coinsToUse);
        
        $coinConversionRate = env('COIN_CONVERSION_RATE', 100);
        $coinEarned = floor($finalAmount / $coinConversionRate);        
    
        $order = Order::create([
            'user_id' => $user->id,
            'cart_details' => json_encode($cart),
            'total_price' => $totalPrice,
            'used_coins' => $coinsToUse, 
            'coin_earned' => $coinEarned,
            'status' => '2',
        ]);
    
        session()->put('order_id', $order->id);
    
        return redirect()->route('game.checkout.view', ['order_id' => session('order_id')]);
    }
    

    // public function checkout(Request $request)
    // {
    //     if (!Session::has('user')) {
    //         session()->put('url.intended', route('game.checkout'));
    //         return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบก่อนทำการชำระเงิน');
    //     }
    
    //     $cart = session()->get('cart', []);
    
    //     if (!empty($cart)) {
    //         Order::where('user_id', Session::get('user')->id)
    //             ->where('status', '1') 
    //             ->delete();
    //     } else {
    //         $this->loadCartFromDatabase();
    //         $cart = session()->get('cart', []);
    //     }
    
    //     if (empty($cart)) {
    //         return redirect()->route('game.cart.view')->with('error', 'ตะกร้าสินค้าของคุณว่างเปล่า');
    //     }
    
    //     $updatedCart = [];
    //     foreach ($cart as $game_id => $game) {
    //         foreach ($game['packages'] as $package_id => $package) {
    //             $gamePackage = GamePackage::find($package_id);
    //             if ($gamePackage) {
    //                 $updatedCart[$game_id]['game_name'] = $game['game_name'];
    //                 $updatedCart[$game_id]['packages'][$package_id] = [
    //                     'name' => $gamePackage->name,
    //                     'full_price' => $gamePackage->full_price ?? null, 
    //                     'price' => $gamePackage->selling_price,
    //                     'player_id' => $package['player_id'],
    //                 ];
    //             }
    //         }
    //     }
    
    //     session()->put('cart', $updatedCart);
    //     $user = Session::get('user');
    //     $totalPrice = collect($updatedCart)->pluck('packages')->flatten(1)->sum('price');
    //     $useCoins = session()->get('use_coins', 0);
    
    //     $coinsToUse = $request->has('use_coins') && $request->input('use_coins') == 1 ? min($coinsAvailable, $maxDiscount) : 0;
    //     $finalAmount = $totalPrice - $coinsToUse;

    //     $order = Order::create([
    //         'user_id' => Session::get('user')->id,
    //         'cart_details' => json_encode($updatedCart),
    //         'total_price' => $totalPrice,
    //         'used_coins' => $coinsToUse,
    //         'status' => '2', 
    //     ]);
    //     session()->put('order_id', $order->id);
    
    //     return redirect()->route('game.checkout.view', ['order_id' => session('order_id')]);
    // }

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
    
        $cartDetails = json_decode($order->cart_details, true);
        $totalAmount = collect($cartDetails)->pluck('packages')->flatten(1)->sum('price');

        $usedCoins = $order->used_coins ?? 0;

        $finalAmount = $totalAmount - $usedCoins;

        return view('checkout', compact('order', 'totalAmount', 'usedCoins', 'finalAmount'));

        // if (request()->has('confirm_checkout')) {
        //     session()->forget(['cart', 'order_id']);
        // }
    
        // return view('checkout', compact('order'));
    }     
    
    public function confirmPayment(Request $request, $order_id)
    {
        if (!Session::has('user')) {
            return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบก่อนแนบสลิปการชำระเงิน');
        }
    
        $request->validate([
            'payment_slip' => 'required|mimes:jpeg,png,pdf|max:2048',
        ]);
    
        $order = Order::where('id', $order_id)
                      ->where('user_id', Session::get('user')->id)
                      ->where('status', '2') 
                      ->first();
    
        if (!$order) {
            return redirect()->route('game.cart.view')->with('error', 'ไม่พบคำสั่งซื้อนี้');
        }

        if ($order->used_coins > 0) {
            \App\Models\User::where('id', Session::get('user')->id)->decrement('coins', $order->used_coins);
        }        
    
        $filePath = $request->file('payment_slip')->store('payments', 'public');
    
        $order->update([
            'payment_slip' => $filePath,
            'status' => '3', 
        ]);
    
        session()->forget(['cart', 'order_id']);
    
        return redirect()->route('dashboard')->with('success', 'ได้รับคำสั่งซื้อแล้ว.');
    }
    
}
