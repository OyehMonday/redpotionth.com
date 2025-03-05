<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GamePackage;
use App\Models\Game;
use Illuminate\Support\Facades\Session;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log; 
use App\Services\LineNotificationService;
use Illuminate\Support\Facades\Storage;
use App\Services\PaymentSlipService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GameCartController extends Controller
{
    
    public function addToCart(Request $request)
    {
        $package = GamePackage::findOrFail($request->package_id);
        $game = Game::findOrFail($package->game_id);
    
        // If the user is NOT logged in, store in session
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
    
            return redirect()->route('game.cart.view')->with('success', 'สินค้าเพิ่มลงในตะกร้าแล้ว');
        }
    
        // If user is logged in, update the order in the database
        $user = Session::get('user');
    
        // 🛠 Find existing order with status '1' (active cart) OR '2' (checkout started)
        $existingOrder = Order::where('user_id', $user->id)
                              ->whereIn('status', ['1', '2']) 
                              ->first();
    
        $cartData = [];
    
        if ($existingOrder) {
            $cartData = json_decode($existingOrder->cart_details, true);
        }
    
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
    
        if ($existingOrder) {
            // 🛠 If the order exists (status = 1 or 2), update it and set status to '1'
            $existingOrder->update([
                'cart_details' => json_encode($cartData),
                'total_price' => collect($cartData)->pluck('packages')->flatten(1)->sum('price'),
                'status' => '1' // Ensure it reverts back to '1'
            ]);
        } else {
            // 🛠 If no order exists, create a new one
            $order = Order::create([
                'user_id' => $user->id,
                'cart_details' => json_encode($cartData),
                'total_price' => $package->selling_price,
                'status' => '1',
            ]);
    
            session()->put('order_id', $order->id);
        }
    
        return redirect()->route('game.cart.view')->with('success', 'สินค้าเพิ่มลงในตะกร้าแล้ว');
    }
    
    
    // public function addToCart(Request $request)
    // {
    //     $package = GamePackage::findOrFail($request->package_id);
    //     $game = Game::findOrFail($package->game_id);
    
    //     if (!Session::has('user')) {
    //         $cart = session()->get('cart', []);
    
    //         if (!isset($cart[$game->id])) {
    //             $cart[$game->id] = [
    //                 'game_name' => $game->title,
    //                 'player_id' => '',
    //                 'uid_detail' => $game->uid_detail,
    //                 'packages' => []
    //             ];
    //         }
    
    //         $uniqueId = uniqid($package->id . '_', true);
    
    //         $cart[$game->id]['packages'][$uniqueId] = [
    //             'unique_id' => $uniqueId,
    //             'package_id' => $package->id,
    //             'name' => $package->name,
    //             'detail' => $package->detail,
    //             'price' => $package->selling_price,
    //             'full_price' => $package->full_price,
    //             'cover_image' => $package->cover_image
    //         ];
    
    //         session()->put('cart', $cart);
    
    //         return redirect()->route('game.cart.view')->with('success', '');
    //     }
    
    //     $user = Session::get('user');
    //     $existingOrder = Order::where('user_id', $user->id)->where('status', '1')->first();
    
    //     if (!$existingOrder) {
    //         $cartData = [
    //             $game->id => [
    //                 'game_name' => $game->title,
    //                 'player_id' => '',
    //                 'uid_detail' => $game->uid_detail,
    //                 'packages' => []
    //             ]
    //         ];
    
    //         $uniqueId = uniqid($package->id . '_', true);
    
    //         $cartData[$game->id]['packages'][$uniqueId] = [
    //             'unique_id' => $uniqueId,
    //             'package_id' => $package->id,
    //             'name' => $package->name,
    //             'detail' => $package->detail,
    //             'price' => $package->selling_price,
    //             'full_price' => $package->full_price,
    //             'cover_image' => $package->cover_image
    //         ];
    
    //         $order = Order::create([
    //             'user_id' => $user->id,
    //             'cart_details' => json_encode($cartData),
    //             'total_price' => $package->selling_price,
    //             'status' => '1', 
    //         ]);
    
    //         session()->put('order_id', $order->id);
    //     } else {
    //         $cartData = json_decode($existingOrder->cart_details, true);
    
    //         if (!isset($cartData[$game->id])) {
    //             $cartData[$game->id] = [
    //                 'game_name' => $game->title,
    //                 'player_id' => '',
    //                 'uid_detail' => $game->uid_detail,
    //                 'packages' => []
    //             ];
    //         }
    
    //         $uniqueId = uniqid($package->id . '_', true);
    
    //         $cartData[$game->id]['packages'][$uniqueId] = [
    //             'unique_id' => $uniqueId,
    //             'package_id' => $package->id,
    //             'name' => $package->name,
    //             'detail' => $package->detail,
    //             'price' => $package->selling_price,
    //             'full_price' => $package->full_price,
    //             'cover_image' => $package->cover_image
    //         ];
    
    //         $existingOrder->update([
    //             'cart_details' => json_encode($cartData),
    //             'total_price' => collect($cartData)->pluck('packages')->flatten(1)->sum('price'),
    //         ]);
    //     }
    
    //     return redirect()->route('game.cart.view')->with('success', '');
    // }

    public function viewCart()
    {
        $cart = [];

        if (!Session::has('user')) {
            $cart = session()->get('cart', []);
        } else {
            $user = Session::get('user');
            $existingOrder = Order::where('user_id', $user->id)
                                ->whereIn('status', ['1', '2']) // Fetch cart or checkout started orders
                                ->first();

            if ($existingOrder) {
                $cart = json_decode($existingOrder->cart_details, true);

                // 🛠 If order is in status '2' (checkout started), change it back to '1' (active cart)
                if ($existingOrder->status == '2') {
                    $existingOrder->update(['status' => '1']);
                }
            }
            
            $today = Carbon::now()->format('l'); 
            $businessHours = DB::table('business_hours')
                                ->where('day', $today)
                                ->first();

            return view('cart', compact('cart', 'businessHours'));            

            if (session()->has('cart') && !empty(session('cart'))) {
                $sessionCart = session('cart', []);

                foreach ($sessionCart as $game_id => $game) {
                    if (!isset($cart[$game_id])) {
                        $cart[$game_id] = $game;
                    } else {
                        foreach ($game['packages'] as $package_id => $package) {
                            $cart[$game_id]['packages'][$package_id] = $package;
                        }
                    }
                }

                // Update order with new items
                if ($existingOrder) {
                    $existingOrder->update([
                        'cart_details' => json_encode($cart),
                        'total_price' => collect($cart)->pluck('packages')->flatten(1)->sum('price'),
                        'status' => '1' // Ensure status stays '1' when adding more items
                    ]);
                } else {
                    // Create a new order if one doesn't exist
                    $order = Order::create([
                        'user_id' => $user->id,
                        'cart_details' => json_encode($cart),
                        'total_price' => collect($cart)->pluck('packages')->flatten(1)->sum('price'),
                        'status' => '1',
                    ]);
                    session()->put('order_id', $order->id);
                }

                // Clear session cart after merging to avoid conflicts
                session()->forget('cart');
            }
        }

        return view('cart', compact('cart'));
    }

    // 3/3/2025 15.57
    // public function viewCart()
    // {
    //     if (!Session::has('user')) {
    //         $cart = session()->get('cart', []);
    //         return view('cart', compact('cart'));
    //     }
    
    //     $user = Session::get('user');
    
    //     $order = Order::where('user_id', $user->id)
    //                   ->where('status', '>=', 3)
    //                   ->first();
    
    //     if ($order) {
    //         session()->forget(['cart', 'order_id']);
    //         return view('cart', ['cart' => []])->with('error', 'ไม่มีสินค้าในตะกร้า');
    //     }
    
    //     $cart = session()->get('cart', []);
    
    //     if (empty($cart)) {
    //         $this->loadCartFromDatabase();
    //         $cart = session()->get('cart', []);
    //     }
    
    //     return view('cart', compact('cart'));
    // }
    

    // public function viewCart()
    // {
    //     if (!Session::has('user')) {
    //         return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบก่อนทำการชำระเงิน');
    //     }
    
    //     $user = Session::get('user');
    
    //     $order = Order::where('user_id', $user->id)
    //                   ->where('status', '>=', 3)
    //                   ->first();
    
    //     if ($order) {
    //         session()->forget(['cart', 'order_id']);
    
    //         // ✅ Instead of redirecting immediately, return a view showing "Cart is empty"
    //         return view('cart', ['cart' => []])->with('error', 'ไม่มีสินค้าในตะกร้า');
    //     }
    
    //     $cart = session()->get('cart', []);
    //     return view('cart', compact('cart'));
    // }
    

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
                    return redirect()->route('game.cart.view')->with('success', 'ตะกร้าสินค้าถูกลบทั้งหมด');
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
                              ->whereIn('status', ['1', '2']) 
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

    }     
    // 04/03/2024 working code
    // public function confirmPayment(Request $request, $order_id)
    // {
    //     if (!Session::has('user')) {
    //         return redirect()->route('custom.login.form')->with('error', 'กรุณาเข้าสู่ระบบก่อนแนบสลิปการชำระเงิน');
    //     }
    
    //     $request->validate([
    //         'payment_slip' => 'required|mimes:jpeg,png,pdf|max:2048',
    //     ]);
    
    //     $order = Order::where('id', $order_id)
    //                   ->where('user_id', Session::get('user')->id)
    //                   ->where('status', '2') 
    //                   ->first();
    
    //     if (!$order) {
    //         return redirect()->route('game.cart.view')->with('error', 'ไม่พบคำสั่งซื้อนี้');
    //     }
    
    //     if ($order->used_coins > 0) {
    //         User::where('id', Session::get('user')->id)->decrement('coins', $order->used_coins);
    //     }
    
    //     $year = date('Y');
    //     $month = date('m');
    //     $folderPath = base_path("images/payments/{$year}/{$month}"); 
    
    //     if (!file_exists($folderPath)) {
    //         mkdir($folderPath, 0777, true); 
    //     }
    
    //     $fileName = time() . '_' . $request->file('payment_slip')->getClientOriginalName(); 
    
    //     $request->file('payment_slip')->move($folderPath, $fileName);
    
    //     $filePath = "payments/{$year}/{$month}/" . $fileName;
    
    //     $order->update([
    //         'payment_slip' => $filePath,
    //         'status' => '3', 
    //     ]);
    
    //     $lineService = new LineNotificationService();
    //     $slipUrl = url('images/' . str_replace(base_path('images/'), '', $filePath)); 
    
    //     $message = "มี Order ใหม่ !!\n\n".
    //                "🛒 หมายเลขคำสั่งซื้อ: {$order->id}\n".
    //                "👤 ผู้ซื้อ: " . Session::get('user')->username . "\n".
    //                "💰 ยอดโอน: {$order->total_price} บาท\n".
    //                "📸 สลิป: $slipUrl";
    
    //     $lineService->sendMessage($message);
    
    //     session()->forget(['cart', 'order_id']);
        
    //     return redirect()->route('dashboard')->with('success', 'ได้รับคำสั่งซื้อแล้ว');
    // }

    
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
            User::where('id', Session::get('user')->id)->decrement('coins', $order->used_coins);
        }
    
        $year = date('Y');
        $month = date('m');
        $folderPath = base_path("images/payments/{$year}/{$month}"); 
    
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $randomFileName = Str::random(12);

        $extension = $request->file('payment_slip')->getClientOriginalExtension();
    
        $fileName = $randomFileName . '.' . $extension;
    
        $request->file('payment_slip')->move($folderPath, $fileName);
        $filePathStore = "payments/{$year}/{$month}/" . $fileName;
        $filePath = "images/payments/{$year}/{$month}/" . $fileName;

        $paymentService = new PaymentSlipService();
        $qrResult = $paymentService->storeQRCodeData($order_id, base_path('images/' . $filePathStore));
    
        $order->update([
            'payment_slip' => $filePathStore,
            'status' => '3',
            'refqr' => $qrResult['refqr'],  
            'referror' => $qrResult['referror'],
        ]);
    
        $lineService = new LineNotificationService();
        $slipUrl = url('images/' . $filePathStore);

        $errorMessage = "";
        if ($qrResult['referror'] == 1) {
            $errorMessage = "⚠️ สลิปอาจซ้ำ";
        } elseif ($qrResult['referror'] == 2) {
            $errorMessage = "⚠️ สลิปไม่มี QR ตรวจสอบไม่ได้";
        }        
    
        $message = "มี Order ใหม่ !!\n\n".
                   "🛒 หมายเลขคำสั่งซื้อ: {$order->id}\n".
                   "👤 ผู้ซื้อ: " . Session::get('user')->username . "\n".
                   "💰 ยอดโอน: {$order->total_price} บาท\n".
                   "📸 สลิป: $slipUrl\n".
                   ($errorMessage ? "{$errorMessage}\n" : "");
    
        $lineService->sendMessage($message);
    
        session()->forget(['cart', 'order_id']);
    
        return redirect()->route('dashboard')->with('success', 'ได้รับคำสั่งซื้อแล้ว');
    }

}
