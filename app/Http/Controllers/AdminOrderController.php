<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\CoinTransaction;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::whereIn('status', [2, 3, 4, 11])
                        ->orderBy('created_at', 'desc') 
                        ->get();
        
        return view('admin.orders.index', compact('orders'));
    }

    // public function approvePayment($orderId)
    // {
    //     $order = Order::find($orderId);
    
    //     if ($order && in_array($order->status, [2, 3])) {
    //         $user = $order->user;
    
    //         $coinsUsed = $order->used_coins; 
    //         $coinsEarned = $order->coin_earned; 
    
    //         $user->coins = $user->coins - $coinsUsed + $coinsEarned;
    //         $user->save();
    
    //         CoinTransaction::create([
    //             'user_id' => $user->id,
    //             'coins_used' => $coinsUsed,
    //             'coin_earned' => $coinsEarned, 
    //             'order_id' => $order->id,
    //         ]);
    
    //         $order->status = 4;
    //         $order->approved_by = auth()->guard('admin')->id(); 
    //         $order->payment_approved_at = now(); 
    //         $order->coin_earned = $coinsEarned; 
    //         $order->save();
    
    //         return redirect()->route('admin.orders.index')->with('success', 'Payment approved and coins updated!');
    //     }
    
    //     return redirect()->route('admin.orders.index')->with('error', 'Invalid order or order not ready for approval.');
    // }


    public function markInProcess($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->in_process_by = auth()->guard('admin')->id(); // Get the ID of the logged-in admin
        $order->save();
    
        $order->status = 11; // 11 means "In Process"
        $order->save();
    
        return redirect()->route('admin.orders.index')->with('success', 'Order is now in process.');
    }    

    // public function markCompleted($orderId)
    // {
    //     $order = Order::find($orderId);

    //     if ($order) {
    //         $order->status = 4; 
    //         $order->save();

    //         $order->approved_by = auth()->guard('admin')->id();
    //         $order->save();
    //     }

    //     return redirect()->back();
    // }

    public function markCompleted($orderId)
    {
        $order = Order::find($orderId);
    
        if ($order) {
            if (in_array($order->status, [2, 3, 11])) {
                $user = $order->user;
    
                $coinsUsed = $order->used_coins;
                $coinsEarned = $order->coin_earned;
    
                // $user->coins = $user->coins - $coinsUsed + $coinsEarned;
                $user->coins = $user->coins + $coinsEarned;
                $user->save();
    
                CoinTransaction::create([
                    'user_id' => $user->id,
                    'coins_used' => $coinsUsed,
                    'coin_earned' => $coinsEarned,
                    'order_id' => $order->id,
                ]);
            }
    
            $order->status = 4; 
            $order->approved_by = auth()->guard('admin')->id();
            $order->payment_approved_at = now();  
            $order->coin_earned = $coinsEarned ?? 0; 
            $order->save();
        }
    
        return redirect()->route('admin.orders.index')->with('success', 'Order marked as completed, payment approved, and coins updated!');
    }
    
    public function getNewOrders(Request $request)
    {
        // Get the latest 5 orders, always
        $orders = Order::orderBy('created_at', 'desc')
                       ->limit(5)
                       ->get();
        
        // Return the orders as JSON
        return response()->json($orders);
    }
    
    
}
