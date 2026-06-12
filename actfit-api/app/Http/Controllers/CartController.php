<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Meal;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // GET /api/cart  — عرض محتويات العربية
    public function index(Request $request)
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        $items = CartItem::with('meal')
            ->where('cart_id', $cart->id)
            ->get()
            ->map(function ($item) {
                return [
                    'cart_item_id' => $item->id,
                    'meal'         => $item->meal,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->quantity * $item->meal->price,
                ];
            });

        $total = $items->sum('subtotal');

        return response()->json([
            'cart_id' => $cart->id,
            'items'   => $items,
            'total'   => $total,
        ]);
    }

    // POST /api/cart  — إضافة وجبة للعربية
    public function addItem(Request $request)
    {
        $data = $request->validate([
            'meal_id'  => 'required|exists:meals,id',
            'quantity' => 'integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        // لو الوجبة موجودة بالفعل → زوّد الكمية
        $item = CartItem::where('cart_id', $cart->id)
                        ->where('meal_id', $data['meal_id'])
                        ->first();

        if ($item) {
            $item->quantity += $data['quantity'] ?? 1;
            $item->save();
        } else {
            $item = CartItem::create([
                'cart_id'  => $cart->id,
                'meal_id'  => $data['meal_id'],
                'quantity' => $data['quantity'] ?? 1,
            ]);
        }

        return response()->json(['message' => 'Item added to cart', 'item' => $item], 201);
    }

    // PUT /api/cart/{itemId}  — تعديل الكمية
    public function updateItem(Request $request, $itemId)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        $item = CartItem::where('id', $itemId)->where('cart_id', $cart->id)->firstOrFail();

        $item->update(['quantity' => $data['quantity']]);

        return response()->json(['message' => 'Cart updated', 'item' => $item]);
    }

    // DELETE /api/cart/{itemId}  — حذف وجبة من العربية
    public function removeItem(Request $request, $itemId)
    {
        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        $item = CartItem::where('id', $itemId)->where('cart_id', $cart->id)->firstOrFail();

        $item->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }

    // DELETE /api/cart  — مسح العربية كلها
    public function clearCart(Request $request)
    {
        $cart = Cart::where('user_id', $request->user()->id)->firstOrFail();
        CartItem::where('cart_id', $cart->id)->delete();

        return response()->json(['message' => 'Cart cleared']);
    }
}
