<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();
        
        $total = $cartItems->sum(function($item) {
            return $item->quantity * ($item->product ? \App\Services\Catalog\PricingService::resolveCustomerPrice($item->product) : 0);
        });

        return $this->successResponse([
            'items' => $cartItems,
            'summary' => [
                'total' => $total,
                'count' => $cartItems->sum('quantity')
            ]
        ], 'Cart retrieved successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        
        // Find existing cart item for this user
        $cartItem = Cart::where('user_id', $user->id)
                        ->where('product_id', $validated['product_id'])
                        ->first();

        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            $cartItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity']
            ]);
        }

        return $this->successResponse($cartItem, 'Product added to cart', 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $user = $request->user();
        $cartItem = Cart::where('user_id', $user->id)->where('id', $id)->first();

        if (!$cartItem) {
            return $this->errorResponse('Cart item not found', 404);
        }

        $cartItem->update(['quantity' => $validated['quantity']]);

        return $this->successResponse($cartItem, 'Cart item updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $cartItem = Cart::where('user_id', $user->id)->where('id', $id)->first();
        
        if ($cartItem) {
            $cartItem->delete();
            return $this->successResponse(null, 'Item removed from cart');
        }

        return $this->errorResponse('Cart item not found', 404);
    }
}
