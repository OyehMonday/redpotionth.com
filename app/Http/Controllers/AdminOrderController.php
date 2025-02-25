<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\CoinTransaction;
use App\Models\Admin;
use Illuminate\Support\Facades\Storage;

class AdminOrderController extends Controller
{

    public function index()
    {
        $orders = Order::with('inProcessBy') 
                        ->whereIn('status', [2, 3, 4, 11, 99])
                        ->orderBy('created_at', 'desc')
                        ->get();
    
        return view('admin.orders.index', compact('orders'));
    }
    

    // public function index()
    // {
    //     $orders = Order::whereIn('status', [2, 3, 4, 11])
    //                     ->orderBy('created_at', 'desc') 
    //                     ->get();
        
    //     return view('admin.orders.index', compact('orders'));
    // }

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

    public function markInProcess(Order $order)
    {
        try {
            $order->in_process_by = auth()->guard('admin')->id();
            $order->status = 11; 
            $order->save();
    
            return response()->json(['success' => true, 'message' => 'Order is now in process.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // public function markInProcess($orderId)
    // {
    //     $order = Order::findOrFail($orderId);
    //     $order->in_process_by = auth()->guard('admin')->id(); // Get the ID of the logged-in admin
    //     $order->save();
    
    //     $order->status = 11; // 11 means "In Process"
    //     $order->save();
    
    //     return redirect()->route('admin.orders.index')->with('success', '');
    // }    

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
        try {
            $order = Order::find($orderId);
    
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
            }
    
            if (in_array($order->status, [2, 3, 11, 99])) {
                $user = $order->user;
                $coinsUsed = $order->used_coins ?? 0;
                $coinsEarned = $order->coin_earned ?? 0;
    
                if ($user) {
                    $user->coins += $coinsEarned;
                    $user->save();
    
                    CoinTransaction::create([
                        'user_id' => $user->id,
                        'coins_used' => $coinsUsed,
                        'coin_earned' => $coinsEarned,
                        'order_id' => $order->id,
                    ]);
                }
            }
    
            $order->status = 4; 
            $order->approved_by = auth()->guard('admin')->id();
            $order->payment_approved_at = now();  
            $order->save();
    
            return response()->json([
                'success' => true,
                'message' => 'ออเดอร์สำเร็จ ตรวจสลิปแล้ว',
                'order_id' => $order->id,
                'new_status' => 4, 
            ]);
    
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    

    // public function markCompleted($orderId)
    // {
    //     $order = Order::find($orderId);
    
    //     if ($order) {
    //         if (in_array($order->status, [2, 3, 11])) {
    //             $user = $order->user;
    
    //             $coinsUsed = $order->used_coins;
    //             $coinsEarned = $order->coin_earned;
    
    //             $user->coins = $user->coins + $coinsEarned;
    //             $user->save();
    
    //             CoinTransaction::create([
    //                 'user_id' => $user->id,
    //                 'coins_used' => $coinsUsed,
    //                 'coin_earned' => $coinsEarned,
    //                 'order_id' => $order->id,
    //             ]);
    //         }
    
    //         $order->status = 4; 
    //         $order->approved_by = auth()->guard('admin')->id();
    //         $order->payment_approved_at = now();  
    //         $order->coin_earned = $coinsEarned ?? 0; 
    //         $order->save();
    //     }
    
    //     return redirect()->route('admin.orders.index')->with('success', 'Order marked as completed, payment approved, and coins updated!');
    // }

    public function getNewOrders(Request $request)
    {
        $perPage = 10;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $unfinishedOnly = $request->input('unfinished_only', false); 
    
        $query = Order::with('user')->whereIn('status', [2, 3, 4, 11, 99]);
    
        if ($unfinishedOnly) {
            $query->whereNotIn('status', [4, 99]);
        }
    
        $unfinishedOrdersCount = Order::whereIn('status', [2, 3, 11])->count();
        $totalOrders = $query->count(); 
    
        $orders = $query->orderBy('created_at', 'desc')
                        ->offset($offset)
                        ->limit($perPage)
                        ->get();
    
        foreach ($orders as $order) {
            $order->admin_name = $order->in_process_by 
                ? optional(Admin::find($order->in_process_by))->name 
                : null;
    
            $order->approved_by_name = $order->approved_by 
                ? optional(Admin::find($order->approved_by))->name 
                : null;
    
            $order->canceled_by_name = $order->canceled_by 
                ? optional(Admin::find($order->canceled_by))->name 
                : null;
        }
    
        return response()->json([
            'orders' => $orders,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_orders' => $totalOrders, 
            'total_pages' => ceil($totalOrders / $perPage), 
            'unfinished_orders' => $unfinishedOrdersCount, 
        ]);
    }        

    // public function getNewOrders(Request $request)
    // {
    //     $perPage = 10; 
    //     $page = $request->input('page', 1); 
    //     $offset = ($page - 1) * $perPage; 
    //     $unfinishedOrdersCount = Order::whereIn('status', [2, 3, 11])->count();
    //     $totalOrders = Order::whereIn('status', [2, 3, 4, 11])->count();
    
    //     $orders = Order::with('user')
    //                    ->whereIn('status', [2, 3, 4, 11])
    //                    ->orderBy('created_at', 'desc')
    //                    ->offset($offset)
    //                    ->limit($perPage)
    //                    ->get();
    
    //     foreach ($orders as $order) {
    //         $order->admin_name = $order->in_process_by 
    //             ? optional(Admin::find($order->in_process_by))->name 
    //             : null;
    
    //         $order->approved_by_name = $order->approved_by 
    //             ? optional(Admin::find($order->approved_by))->name 
    //             : null;
    //     }
    
    //     return response()->json([
    //         'orders' => $orders,
    //         'current_page' => $page,
    //         'per_page' => $perPage,
    //         'total_orders' => $totalOrders,
    //         'total_pages' => ceil($totalOrders / $perPage),
    //         'unfinished_orders' => $unfinishedOrdersCount,
    //     ]);
    // }   

    public function cancelOrder($orderId)
    {
        try {
            $order = Order::find($orderId);
    
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
            }
    
            $user = $order->user;
            $coinsUsed = $order->used_coins ?? 0;
            $coinsEarned = $order->coin_earned ?? 0;
    
            if ($user) {
                $user->coins = max(0, $user->coins + $coinsUsed);
                $user->save();
            }
    
            CoinTransaction::create([
                'user_id' => $user->id,
                'coins_used' => -$coinsUsed,
                'coin_earned' => -$coinsEarned,
                'order_id' => $order->id,
                'transaction_type' => 'cancellation',
            ]);
    
            $admin = auth()->guard('admin')->user();
            $order->status = 99;
            $order->canceled_by = $admin->id;
            $order->save();
    
            return response()->json([
                'success' => true,
                'message' => 'Order has been canceled, and coins have been adjusted.',
                'order_id' => $order->id,
                'canceled_by_name' => $admin->name,
            ]);
    
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
    public function showOrderDetails($orderId)
    {
        $order = Order::with('user')->find($orderId);

        if (!$order) {
            abort(404, 'Order not found');
        }
    
        $admin = $order->in_process_by ? Admin::find($order->in_process_by) : null;
        $approvedAdmin = $order->approved_by ? Admin::find($order->approved_by) : null;
        $canceledAdmin = $order->canceled_by ? Admin::find($order->canceled_by) : null;
    
        $order->admin_name = $admin ? $admin->name : null;
        $order->approved_by_name = $approvedAdmin ? $approvedAdmin->name : null;
        $order->canceled_by_name = $canceledAdmin ? $canceledAdmin->name : null;
    
        return view('admin.orders/order-details', compact('order', 'admin'));
    }
    


}
