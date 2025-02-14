<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::whereIn('status', [2, 3, 4])
                        ->orderBy('created_at', 'desc') 
                        ->get();
        
        return view('admin.orders.index', compact('orders'));
    }

    public function approvePayment($orderId)
    {
        $order = Order::find($orderId);
    
        if ($order && $order->status == 3) {
            $order->status = 4;
            $order->approved_by = auth()->guard('admin')->id(); 
            $order->payment_approved_at = now();
            $order->save();
    
            return redirect()->route('admin.orders.index')->with('success', 'Payment approved!');
        }
    
        return redirect()->route('admin.orders.index')->with('error', 'Invalid order or order not ready for approval.');
    }
    
}
