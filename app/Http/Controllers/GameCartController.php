<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GamePackage;
use Illuminate\Support\Facades\Session;

class GameCartController extends Controller
{
    public function addToCart(Request $request)
    {
        $package = GamePackage::findOrFail($request->package_id);

        $cart = session()->get('cart', []);

        // If the package already exists in the cart, increase the quantity
        if (isset($cart[$package->id])) {
            $cart[$package->id]['quantity']++;
        } else {
            $cart[$package->id] = [
                'name' => $package->name,
                'price' => $package->selling_price,
                'quantity' => 1
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Package added to cart!');
    }
}
